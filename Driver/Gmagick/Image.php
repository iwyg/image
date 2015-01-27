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
use GmagickException;
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\PaletterInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\GmagickFilter;
use Thapp\Image\Palette\PaletteInterface;
use Thapp\Image\Info\MetaDataInterface;
use Thapp\Image\Info\MetaData;

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
    private $meta;
    private static $filterMap;

    /**
     * Constructor.
     *
     * @param Gmagick $gmagick
     *
     * @return void
     */
    public function __construct(Gmagick $gmagick, PaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->gmagick = $gmagick;
        $this->palette = $palette;
        $this->frames  = new Frames($this);
        $this->meta    = $meta ?: new MetaData([]);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->destroy();
    }

    public function __clone()
    {
        $this->gmagick = clone $this->gmagick;
        $this->meta = clone $this->meta;
        $this->palette = clone $this->palette;
        $this->frames = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
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
        $gmagick->newImage($this->getWitdt(), $this->getHeight(), $this->gmagick->getImageBackgroundColor());

        if (null === $format && $fmt = $this->getFormat()) {
            $format = $fmt;
        }

        if (null !== $format) {
            $gmagick->setImageFormat($fmt);
        }

        return new static($gmagick, clone $this->palette);
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

        $size = $this->getSize();
        $background = $this->gmagick->getImageBackGroundColor()->getColor();

        if (!in_array($format, ['png', 'gif', 'tiff']) ) {
            // preserve color apearance when flatten images
            $this->edit()->canvas($size, new Point(0, 0), $this->palette->getColor([255, 255, 255, 1]));
            $this->gmagick->flattenImages();
        }

        if ($this->hasFrames()) {
            $this->frames()->merge();
            // always flatten images:

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
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * getNumberImages
     *
     * @return void
     */
    private function getNumberImages()
    {
        try {
            return $this->getGmagick()->getNumberImages();
        } catch (GmagickException $e) {
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
