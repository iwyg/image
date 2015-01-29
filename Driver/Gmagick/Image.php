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
use GmagickPixelException;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\PaletterInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\GmagickFilter;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Info\MetaDataInterface;
use Thapp\Image\Info\MetaData;
use Thapp\Image\Exception\ImageException;

/**
 * @class Image
 *
 * @package Thapp\Image\Driver\Gmagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    /**
     * The Gmagick instance
     *
     * @var Gmagick
     */
    private $gmagick;

    /**
     * Documentation at http://php.net/manual/en/gmagickpixel.getcolorvalue.php
     * is wrong. Gmagick::getcolorvalue needs a Gmagick::COLOR_* constant, not
     * a Gmagick::CHANNEL_* constant.
     *
     * Gmagick::COLOR_ALPHA will error. Use Gmagick::COLOR_OPACITY and reverse results.
     *
     * @var array
     */
    private static $colorMap = [
        ColorInterface::CHANNEL_RED     => Gmagick::COLOR_RED,
        ColorInterface::CHANNEL_GREEN   => Gmagick::COLOR_GREEN,
        ColorInterface::CHANNEL_BLUE    => Gmagick::COLOR_BLUE,
        ColorInterface::CHANNEL_ALPHA   => Gmagick::COLOR_OPACITY,
        ColorInterface::CHANNEL_CYAN    => Gmagick::COLOR_CYAN,
        ColorInterface::CHANNEL_MAGENTA => Gmagick::COLOR_MAGENTA,
        ColorInterface::CHANNEL_YELLOW  => Gmagick::COLOR_YELLOW,
        ColorInterface::CHANNEL_KEY     => Gmagick::COLOR_BLACK,
        ColorInterface::CHANNEL_GRAY    => Gmagick::COLOR_RED
    ];

    /**
     * Constructor.
     *
     * @param Gmagick $gmagick
     */
    public function __construct(Gmagick $gmagick, PaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->gmagick = $gmagick;
        $this->palette = $palette;
        $this->frames  = new Frames($this);
        $this->meta    = $meta ?: new MetaData([]);
    }

    public function __clone()
    {
        $this->gmagick = clone $this->gmagick;
        $this->meta = clone $this->meta;
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
    public function newImage($format = null, ColorInterface $color = null)
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
    public function getColorAt(PointInterface $pixel)
    {
        if (!$this->getSize()->has($pixel)) {
            throw new \OutOfBoundsException('Sample is outside of image.');
        }

        // Gmagick does not have a dedicated method for retreiving pixel colors
        // at a given point:
        try {
            $magick = clone $this->gmagick;
            $magick->cropImage(1, 1, $pixel->getX(), $pixel->getY());
            $colors = $magick->getImageHistogram();
        } catch (GmagickException $e) {
            throw new ImageException('Unable to retrive color sample', $e->getCode(), $e);
        }

        $px = array_shift($colors);

        $magick->clear();
        $magick->destroy();
        unset($magick, $colors);

        return $this->colorFromPixel($px);
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
     * pixelToColor
     *
     * @param ImagickPixel $px
     *
     * @return void
     */
    private function colorFromPixel(GmagickPixel $px)
    {
        $colorMap =& static::$colorMap;
        $multiply = $this->palette instanceof CmykPaletteInterface ? 100 : 255;

        $colors = array_map(function ($color) use ($colorMap, $px, $multiply) {
            if (!isset($colorMap[$color])) {
                throw new \RuntimeException;
            }

            // GmagickPixel will throw an exception when using Gmagick::COLOR_ALPHA, instead use color
            // opacity and reverse result.
            $value = $px->getColorValue($colorMap[$color]);

            return ColorInterface::CHANNEL_ALPHA === $color ? 1 - (float)$value : ($value * $multiply);

        }, $keys = $this->palette->getDefinition());

        return $this->palette->getColor(array_combine($keys, $colors));
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
}
