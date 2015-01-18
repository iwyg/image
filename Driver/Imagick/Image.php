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
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\ImagickFilter;

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

        if (null !== $color) {
            $this->imagick->setImageBackgroundColor(new ImagickPixel((string)$color));
        }

        $this->imagick->extentImage($size->getWidth(), $size->getHeight(), $start->getX(), $start->getY());
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
    public function resize(BoxInterface $size)
    {
        $this->imagick->resizeImage($size->getWidth(), $size->getHeight(), Imagick::FILTER_CUBIC, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function crop(BoxInterface $size, PointInterface $crop = null, ColorInterface $color = null)
    {
        if (null !== $crop) {
            $box = new Box($this->getWidth(), $this->getHeight());

            if (!$box->contains($size)) {
                $crop = $crop->negate();
            }
        }

        $this->extent($size, $crop, $color);
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

        if ($this->hasFrames()) {

            $this->frames()->merge();
            $this->imagick->setImageFormat($format);
            $this->imagick->setImageCompressionQuality($this->getOption($options, 'quality', 80));

            if ($this->getOption($options, 'flatten', false)) {
                $this->imagick->flattenImages();
            } elseif (in_array($format, ['gif'])) {
                return $this->imagick->getImagesBlob();
            }
        }

        $this->imagick->setImageFormat($format);

        return $this->imagick->getImageBlob();
    }

    /**
     * applyExtent
     *
     * @param BoxInterface $size
     * @param PointInterface $start
     *
     * @return void
     */
    protected function applyExtent(BoxInterface $size, PointInterface $start)
    {
        $this->imagick->setImageBackgroundColor(new ImagickPixel('red'));
        $this->imagick->extentImage($size->getWidth(), $size->getHeight(), $start->getX(), $start->getY());
    }
}
