<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Imagick;
use ImagickPixel;
use ImagickException;
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\ImagickFilter;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Color\Palette\RgbPaletteInterface;
use Thapp\Image\Color\Palette\CmykPaletteInterface;
use Thapp\Image\Color\Palette\GrayscalePaletteInterface;
use Thapp\Image\Info\MetaData;
use Thapp\Image\Info\MetaDataInterface;

/**
 * @class Image
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    private $imagick;
    private $meta;
    private static $filterMap;
    private static $orientMap;
    private static $colorMap;

    /**
     * Constructor.
     *
     * @param Imagick $imagick
     *
     * @return void
     */
    public function __construct(Imagick $imagick, PaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->imagick = $imagick;
        $this->palette  = $palette;
        $this->meta  = $meta ?: new MetaData([]);
        $this->frames  = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * Returns a copy of this image.
     *
     * @return void
     */
    public function __clone()
    {
        $this->imagick  = $this->cloneImagick();
        $this->meta     = clone $this->meta;
        $this->palette  = clone $this->palette;
        $this->frames   = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        if (null === $this->imagick) {
            return false;
        }

        $this->imagick->clear();
        $this->imagick->destroy();
        $this->imagick = null;

        $this->frames = null;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAt(PointInterface $pixel)
    {
        if (!$this->getSize()->has($pixel)) {
            throw new \OutOfBoundsException('Sample is outside of image.');
        }

        return $this->pixelToColor($this->imagick->getImagePixelColor($pixel->getX(), $pixel->getY()));
    }

    private function pixelToColor(ImagickPixel $px)
    {
        $colorMap = static::colorMap();
        $multiply = $this->palette instanceof CmykPaletteInterface ? 100 : 255;

        $colors = array_map(function ($color) use ($colorMap, $px, $multiply) {
            if (!isset($colorMap[$color])) {
                throw new \RuntimeException;
            }

            $value = $px->getColorValue($colorMap[$color]);

            return ColorInterface::CHANNEL_ALPHA === $color ? (float)$value : ($value * $multiply);

        }, $keys = $this->palette->getDefinition());

        return $this->palette->getColor(array_combine($keys, $colors));
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getPalette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrames()
    {
        return 1 < $this->imagick->getNumberImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->imagick->current()->getImageWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->imagick->current()->getImageHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        if (null === $this->format) {
            $this->format = $this->imagick->getImageFormat();
        }

        return parent::getFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrientation()
    {
        if (!$orient = $this->meta->get($key = 'ifd0.Orentation')) {
            $map = static::orientMap();
            $this->meta->set($key, $orient = $map[$this->imagick->getImageOrientation()]);
        }

        return $this->mapOrientation($orient);
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null)
    {
        $imagick = new Imagick;
        $imagick->newImage($this->getWitdt(), $this->getHeight(), $this->imagick->getImageBackgroundColor());

        if (null === $format && $fmt = $this->getFormat()) {
            $format = $fmt;
        }

        if (null !== $format) {
            $imagick->setImageFormat($fmt);
        }

        return new static($imagick, clone $this->palette);
    }

    /**
     * Get the current imagick resource
     *
     * @return Imagick
     */
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * Swaps the current imagick resource.
     *
     * @param Imagick $imagick
     *
     * @return void
     */
    public function swapImagick(Imagick $imagick)
    {
        if (null !== $this->imagick) {

            $map = array_flip(static::orientMap());
            $orient = $map[$this->getOrientation()];

            $this->imagick->clear();
            $this->imagick->destroy();

            try {
                $imagick->setImageOrientation($orient);
            } catch (ImagickException $orient) {
            }
        }

        $this->imagick = $imagick;
    }

    /**
     * {@inheritdoc}
     */
    public function frames()
    {
        return $this->frames;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlob($imageFormat = null, array $options = [])
    {
        return $this->get($imageFormat, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function get($format = null, array $options = [])
    {
        if (null === $format) {
            $format = isset($options['format']) ? $options['format'] : $this->getFormat();
        }

        $background = $this->imagick->getImageBackGroundColor()->getColor();

        if (!in_array($format, ['png', 'gif', 'tiff']) ) {
            // preserve color apearance when flatten images
            if (Imagick::ALPHACHANNEL_ACTIVATE === $this->imagick->getImageAlphaChannel()) {
                $this->edit()->canvas($this->getSize(), new Point(0, 0), $this->palette->getColor([255, 255, 255, 1]));
            }
            $this->imagick->flattenImages();
        }

        if ($this->hasFrames()) {

            $this->frames()->merge();
            $this->imagick->setImageFormat($format);
            $this->imagick->setImageCompressionQuality($this->getOption($options, 'quality', 80));

            if ($this->getOption($options, 'flatten', false)) {
                $this->imagick->flattenImages();
            } elseif (in_array($format, ['gif'])) {
                //return $this->imagick->getImagesBlob();
            }
        } else {
            $this->imagick->setImageFormat($format);
        }

        return $this->imagick->getImagesBlob();
    }

    /**
     * compositeCopy
     *
     * @param BoxInterface $size
     * @param PointInterface $point
     * @param ColorInterface $color
     *
     * @return void
     */
    protected function compositeCopy(BoxInterface $size, PointInterface $point, ColorInterface $color = null)
    {
        $canvas = new Imagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? (string)$color : 'None');
        $canvas->compositeImage($this->imagick, Imagick::COMPOSITE_OVER, $point->getX(), $point->getY());
        $canvas->setImageFormat($this->imagick->getImageFormat());
        $this->swapImagick($canvas);
    }

    private function cloneImagick()
    {
        if (version_compare(phpversion('imagick'), '3.1.0b1', '>=') || defined('HHVM_VERSION')) {
            return clone $this->imagick;
        }

        return $this->imagick->clone();
    }

    /**
     * getFilter
     *
     * @param int $filter
     *
     * @return int
     */
    private function getFilter($filter)
    {
        $map = static::filterMap();

        if (!isset($map[$filter])) {
            return Imagick::FILTER_UNDEFINED;
        }

        return $map[$filter];
    }

    /**
     * filterMap
     *
     * @return array
     */
    private static function &filterMap()
    {
        if (null === static::$filterMap) {
            static::$filterMap = [
                self::FILTER_UNDEFINED => Imagick::FILTER_UNDEFINED,
                self::FILTER_POINT     => Imagick::FILTER_POINT,
                self::FILTER_BOX       => Imagick::FILTER_BOX,
                self::FILTER_TRIANGLE  => Imagick::FILTER_TRIANGLE,
                self::FILTER_HERMITE   => Imagick::FILTER_HERMITE,
                self::FILTER_HANNING   => Imagick::FILTER_HANNING,
                self::FILTER_HAMMING   => Imagick::FILTER_HAMMING,
                self::FILTER_BLACKMAN  => Imagick::FILTER_BLACKMAN,
                self::FILTER_GAUSSIAN  => Imagick::FILTER_GAUSSIAN,
                self::FILTER_QUADRATIC => Imagick::FILTER_QUADRATIC,
                self::FILTER_CUBIC     => Imagick::FILTER_CUBIC,
                self::FILTER_CATROM    => Imagick::FILTER_CATROM,
                self::FILTER_MITCHELL  => Imagick::FILTER_MITCHELL,
                self::FILTER_LANCZOS   => Imagick::FILTER_LANCZOS,
                self::FILTER_BESSEL    => Imagick::FILTER_BESSEL,
                self::FILTER_SINC      => Imagick::FILTER_SINC
            ];
        }

        return static::$filterMap;
    }

    /**
     * &orientMap
     *
     * @return array
     */
    private static function &orientMap()
    {
        if (null === static::$orientMap) {
            static::$orientMap = [
                self::ORIENT_UNDEFINED   => Imagick::ORIENTATION_UNDEFINED,
                self::ORIENT_TOPLEFT     => Imagick::ORIENTATION_TOPLEFT,
                self::ORIENT_TOPRIGHT    => Imagick::ORIENTATION_TOPRIGHT,
                self::ORIENT_BOTTOMRIGHT => Imagick::ORIENTATION_BOTTOMRIGHT,
                self::ORIENT_BOTTOMLEFT  => Imagick::ORIENTATION_BOTTOMLEFT,
                self::ORIENT_LEFTTOP     => Imagick::ORIENTATION_LEFTTOP,
                self::ORIENT_RIGHTTOP    => Imagick::ORIENTATION_RIGHTTOP,
                self::ORIENT_RIGHTBOTTOM => Imagick::ORIENTATION_RIGHTBOTTOM,
                self::ORIENT_LEFTBOTTOM  => Imagick::ORIENTATION_LEFTBOTTOM
            ];
        }

        return static::$orientMap;
    }

    private static function &colorMap()
    {
        if (null === static::$colorMap) {
            static::$colorMap = [
                ColorInterface::CHANNEL_RED => Imagick::COLOR_RED,
                ColorInterface::CHANNEL_GREEN => Imagick::COLOR_GREEN,
                ColorInterface::CHANNEL_BLUE => Imagick::COLOR_BLUE,
                ColorInterface::CHANNEL_ALPHA => Imagick::COLOR_ALPHA,
                ColorInterface::CHANNEL_CYAN => Imagick::COLOR_CYAN,
                ColorInterface::CHANNEL_MAGENTA => Imagick::COLOR_MAGENTA,
                ColorInterface::CHANNEL_YELLOW => Imagick::COLOR_YELLOW,
                ColorInterface::CHANNEL_KEY => Imagick::COLOR_BLACK,
                ColorInterface::CHANNEL_GRAY => Imagick::COLOR_RED
            ];
        }

        return static::$colorMap;
    }
}
