<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Filter\Filter;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Color\ColorInterface;

/**
 * @class AbstractImage
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractImage implements ImageInterface
{
    protected $format;
    protected $frames;
    protected $gravity;
    protected $palette;

    public function setFormat($format)
    {
        $this->format = $this->mapFormat($format);
    }

    public function save($target, $format = null, array $options = [])
    {
        return file_put_contents($target, $this->get($format, $options));
    }

    public function write($resrouce, $format = null, array $options = [])
    {
        if (!is_resource($resrouce)) {
            return false;
        }

        rewind($resource);
        fwrite($resource, $this->get($format, $options));

        return true;
    }

    public function getFormat()
    {
        return strtolower($this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrames()
    {
        return 1 < count($this->frames());
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        return $this->frames->coalesce();
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return new Box($this->getWidth(), $this->getHeight());
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
    public function crop(BoxInterface $size, PointInterface $crop = null, ColorInterface $color = null)
    {
        if (null !== $crop) {
            $box = new Box($this->getWidth(), $this->getHeight());
            if ($box->contains($size)) {
                $crop = $crop->negate();
            }
        }

        $this->extent($size, $crop, $color);
    }

    /**
     * gravity
     *
     * @param GravityInterface $gravity
     *
     * @return void
     */
    public function gravity(GravityInterface $gravity)
    {
        $this->gravity = $gravity;
    }

    public function getGravity()
    {
        return $this->gravity ?: new Gravity(GravityInterface::GRAVITY_NORTHWEST);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(FilterInterface $filter)
    {
        if (!$filter instanceof Filter && $filter->supports($this)) {
            return false;
        }

        $filter->apply($this);
    }

    /**
     * getStartPoint
     *
     * @param BoxInterface $target
     * @param PointInterface $start
     *
     * @return PointInterface
     */
    protected function getStartPoint(BoxInterface $target, PointInterface $start = null)
    {
        if (null !== $start) {
            return $start;
        }

        if (!$this->hasGravity()) {
            return new Point(0, 0);
        }

        // get the point based on the garvity settings
        $point = $this->gravity->getPoint($size = $this->getSize(), $target);

        return $point->negate();
        //if (!$size->contains($target)) {
        //    return $point->negate();
        //    /*return new Point(abs($point->getX()), abs($point->getY()));*/
        //}

        ///*return new Point(abs($point->getX()), abs($point->getY()));*/
        //return $point;//$point->negate();
    }

    /**
     * hasGravity
     *
     * @return boolean
     */
    protected function hasGravity()
    {
        return null !== $this->gravity;
    }

    protected function mapFormat($format)
    {
        $map = ['jpg' => 'jpeg'];

        if (isset($map[strtolower($format)])) {
            $format = $map[strtolower($format)];
        }

        return strtolower($format);
    }

    protected function getOption(array $options, $option, $default = null)
    {
        if (array_key_exists($option, $options)) {
            return $options[$option];
        }

        return $default;
    }
}
