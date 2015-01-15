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
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;

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
    protected $gravity;

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getFormat()
    {
        return $this->format;
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
        return $this->gravity->getPoint($this->getSize(), $target);
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
}
