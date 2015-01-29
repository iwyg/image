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
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
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
    private static $orientMap = [
        Imagick::ORIENTATION_UNDEFINED   => self::ORIENT_UNDEFINED,
        Imagick::ORIENTATION_TOPLEFT     => self::ORIENT_TOPLEFT,
        Imagick::ORIENTATION_TOPRIGHT    => self::ORIENT_TOPRIGHT,
        Imagick::ORIENTATION_BOTTOMRIGHT => self::ORIENT_BOTTOMRIGHT,
        Imagick::ORIENTATION_BOTTOMLEFT  => self::ORIENT_BOTTOMLEFT,
        Imagick::ORIENTATION_LEFTTOP     => self::ORIENT_LEFTTOP,
        Imagick::ORIENTATION_RIGHTTOP    => self::ORIENT_RIGHTTOP,
        Imagick::ORIENTATION_RIGHTBOTTOM => self::ORIENT_RIGHTBOTTOM,
        Imagick::ORIENTATION_LEFTBOTTOM  => self::ORIENT_LEFTBOTTOM
    ];

    private static $colorMap = [
        ColorInterface::CHANNEL_RED     => Imagick::COLOR_RED,
        ColorInterface::CHANNEL_GREEN   => Imagick::COLOR_GREEN,
        ColorInterface::CHANNEL_BLUE    => Imagick::COLOR_BLUE,
        ColorInterface::CHANNEL_ALPHA   => Imagick::COLOR_ALPHA,
        ColorInterface::CHANNEL_CYAN    => Imagick::COLOR_CYAN,
        ColorInterface::CHANNEL_MAGENTA => Imagick::COLOR_MAGENTA,
        ColorInterface::CHANNEL_YELLOW  => Imagick::COLOR_YELLOW,
        ColorInterface::CHANNEL_KEY     => Imagick::COLOR_BLACK,
        ColorInterface::CHANNEL_GRAY    => Imagick::COLOR_RED
    ];

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
     * Returns a copy of this image.
     *
     * @return void
     */
    public function __clone()
    {
        $this->imagick  = $this->cloneImagick();
        $this->frames   = new Frames($this);
        $this->meta     = clone $this->meta;
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

        return $this->colorFromPixel($this->imagick->getImagePixelColor($pixel->getX(), $pixel->getY()));
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
            $this->meta->set($key, $orient = static::$orientMap[$this->imagick->getImageOrientation()]);
        }

        return $this->mapOrientation($orient);
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null, ColorInterface $color = null)
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

            $map = array_flip(static::$orientMap);
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
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * cloneImagick
     *
     * @return void
     */
    private function cloneImagick()
    {
        if (version_compare(phpversion('imagick'), '3.1.0b1', '>=') || defined('HHVM_VERSION')) {
            return clone $this->imagick;
        }

        return $this->imagick->clone();
    }

    /**
     * pixelToColor
     *
     * @param ImagickPixel $px
     *
     * @return void
     */
    private function colorFromPixel(ImagickPixel $px)
    {
        $colorMap =& static::$colorMap;
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
}
