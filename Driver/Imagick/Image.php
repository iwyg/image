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
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\Rgb;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\ImagickFilter;
use Thapp\Image\Palette\PaletteInterface;

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
    private static $filterMap;

    /**
     * Constructor.
     *
     * @param Imagick $imagick
     *
     * @return void
     */
    public function __construct(Imagick $imagick)
    {
        $this->imagick = $imagick;
        $this->frames  = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->destroy();
    }

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
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * {@inheritdoc}
     */
    public function swapImagick(Imagick $imagick)
    {
        $this->imagick = $imagick;
    }

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
    public function newImage($format = null)
    {
        $imagick = new Imagick;
        $imagick->newImage($this->getWitdt(), $this->getHeight(), $this->getBackgroundColor());

        if (null === $format && $fmt = $this->getFormat()) {
            $format = $fmt;
        }

        if (null !== $format) {
            $imagick->setImageFormat($fmt);
        }

        return new static($imagick);
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
    public function extent(BoxInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $start = $this->getStartPoint($size, $start);
        $color = $this->getSize()->contains($size) ? new Rgb(255, 255, 255, 0) : ($color ?: new Rgb(255, 255, 255, 0));

        $this->compositeCopy($size, $start, $color);

        $this->imagick->setImagePage(0, 0, 0, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function scale($perc)
    {
        return $this->resize($this->getSize()->scale($perc));
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        $this->imagick->rotateImage(new ImagickPixel((string)$color ?: '#ffffff'), (float)$deg);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = self::FILTER_UNDEFINED)
    {
        $this->imagick->resizeImage($size->getWidth(), $size->getHeight(), $this->getFilter($filter), 1);
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
    public function get($format = null, array $options = [])
    {
        if (null === $format) {
            $format = isset($options['format']) ? $options['format'] : $this->getFormat();
        }

        $background = $this->imagick->getImageBackGroundColor()->getColor();

        if (!in_array($format, ['png', 'gif', 'tiff']) ) {
            // preserve color apearance when flatten images
            $this->compositeCopy($this->getSize(), new Point(0, 0), new Rgb(255, 255, 255, 1));
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
                self::FILTER_POINT => Imagick::FILTER_POINT,
                self::FILTER_BOX => Imagick::FILTER_BOX,
                self::FILTER_TRIANGLE => Imagick::FILTER_TRIANGLE,
                self::FILTER_HERMITE => Imagick::FILTER_HERMITE,
                self::FILTER_HANNING => Imagick::FILTER_HANNING,
                self::FILTER_HAMMING => Imagick::FILTER_HAMMING,
                self::FILTER_BLACKMAN => Imagick::FILTER_BLACKMAN,
                self::FILTER_GAUSSIAN => Imagick::FILTER_GAUSSIAN,
                self::FILTER_QUADRATIC => Imagick::FILTER_QUADRATIC,
                self::FILTER_CUBIC => Imagick::FILTER_CUBIC,
                self::FILTER_CATROM => Imagick::FILTER_CATROM,
                self::FILTER_MITCHELL => Imagick::FILTER_MITCHELL,
                self::FILTER_LANCZOS => Imagick::FILTER_LANCZOS,
                self::FILTER_BESSEL => Imagick::FILTER_BESSEL,
                self::FILTER_SINC => Imagick::FILTER_SINC
            ];
        }

        return static::$filterMap;
    }
}
