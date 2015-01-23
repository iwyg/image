<?php

/*
 * This File is part of the Thapp\Image\Driver\Gmagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gmagick;

use Gmagick;
use GmagickPixel;
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\Rgb;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\GmagickFilter;
use Thapp\Image\Palette\PaletteInterface;

/**
 * @class Image
 *
 * @package Thapp\Image\Driver\Gmagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    private $gmagick;
    private static $filterMap;

    /**
     * Constructor.
     *
     * @param Gmagick $gmagick
     *
     * @return void
     */
    public function __construct(Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
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
        if (null === $this->gmagick) {
            return false;
        }

        $this->gmagick->clear();
        $this->gmagick->destroy();
        $this->gmagick = null;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getGmagick()
    {
        return $this->gmagick;
    }

    /**
     * {@inheritdoc}
     */
    public function swapGmagick(Gmagick $gmagick)
    {
        $this->gmagick = $gmagick;
    }

    public function hasFrames()
    {
        return 1 < $this->getNumberImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->gmagick->current()->getImageWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->gmagick->current()->getImageHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null)
    {
        $gmagick = new Gmagick;
        $gmagick->newImage($this->getWitdt(), $this->getHeight(), $this->getBackgroundColor());

        if (null === $format && $fmt = $this->getFormat()) {
            $format = $fmt;
        }

        if (null !== $format) {
            $gmagick->setImageFormat($fmt);
        }

        return new static($gmagick);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        if (null === $this->format) {
            $this->format = $this->gmagick->getImageFormat();
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

        /*$this->gmagick->setImagePage(0, 0, 0, 0);*/
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
        $this->gmagick->rotateImage(new GmagickPixel((string)$color ?: '#ffffff'), (float)$deg);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = self::FILTER_UNDEFINED)
    {
        $this->gmagick->resizeImage($size->getWidth(), $size->getHeight(), $this->getFilter($filter), 1);
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

        $background = $this->gmagick->getImageBackGroundColor()->getColor();

        if (!in_array($format, ['png', 'gif', 'tiff']) ) {
            // preserve color apearance when flatten images
            $this->compositeCopy($this->getSize(), new Point(0, 0), new Rgb(255, 255, 255, 1));
            $this->gmagick->flattenImages();
        }

        if ($this->hasFrames()) {

            $this->frames()->merge();
            // always flatten images:
            /*$this->gmagick->setImageFormat($format);*/
            /*$this->gmagick->setCompressionQuality($this->getOption($options, 'quality', 80));*/

            if ($this->getOption($options, 'flatten', false)) {
                $this->gmagick->flattenImages();
            } elseif (in_array($format, ['gif'])) {
                //return $this->gmagick->getImagesBlob();
            }
        } else {
            $this->gmagick->setImageFormat($format);
        }

        $this->gmagick->flattenImages();

        return $this->gmagick->getImagesBlob();
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
        $canvas = new Gmagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? (string)$color : 'None');
        $canvas->compositeImage($this->gmagick, Gmagick::COMPOSITE_OVER, $point->getX(), $point->getY());
        $canvas->setImageFormat($this->gmagick->getImageFormat());
        $this->swapGmagick($canvas);
    }

    private function getNumberImages()
    {
        try {
            return $this->getGmagick()->getNumberImages();
        } catch (\GmagickException $e) {
            throw $e;
        }
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
            return Gmagick::FILTER_UNDEFINED;
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
                self::FILTER_UNDEFINED => Gmagick::FILTER_UNDEFINED,
                self::FILTER_POINT => Gmagick::FILTER_POINT,
                self::FILTER_BOX => Gmagick::FILTER_BOX,
                self::FILTER_TRIANGLE => Gmagick::FILTER_TRIANGLE,
                self::FILTER_HERMITE => Gmagick::FILTER_HERMITE,
                self::FILTER_HANNING => Gmagick::FILTER_HANNING,
                self::FILTER_HAMMING => Gmagick::FILTER_HAMMING,
                self::FILTER_BLACKMAN => Gmagick::FILTER_BLACKMAN,
                self::FILTER_GAUSSIAN => Gmagick::FILTER_GAUSSIAN,
                self::FILTER_QUADRATIC => Gmagick::FILTER_QUADRATIC,
                self::FILTER_CUBIC => Gmagick::FILTER_CUBIC,
                self::FILTER_CATROM => Gmagick::FILTER_CATROM,
                self::FILTER_MITCHELL => Gmagick::FILTER_MITCHELL,
                self::FILTER_LANCZOS => Gmagick::FILTER_LANCZOS,
                self::FILTER_BESSEL => Gmagick::FILTER_BESSEL,
                self::FILTER_SINC => Gmagick::FILTER_SINC
            ];
        }

        return static::$filterMap;
    }
}
