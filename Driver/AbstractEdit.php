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

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;

/**
 * @class AbstractEdit
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractEdit implements EditInterface
{
    const COLOR_NONE = 'None';

    protected $image;

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
    public function crop(SizeInterface $size, PointInterface $crop = null, ColorInterface $color = null)
    {
        if (null !== $crop) {
            $box = new Size($this->getWidth(), $this->getHeight());
            if ($box->contains($size)) {
                $crop = $crop->negate();
            }
        }

        $this->extent($size, $crop, $color);
    }

    /**
     * getStartPoint
     *
     * @param SizeInterface $target
     * @param PointInterface $start
     *
     * @return PointInterface
     */
    protected function getStartPoint(SizeInterface $target, PointInterface $start = null)
    {
        if (null !== $start) {
            return $start;
        }

        $gravity = $this->getImage()->getGravity();

        if (GravityInterface::GRAVITY_NORTHWEST === $gravity->getMode()) {
            return new Point(0, 0);
        }

        // get the point based on the garvity settings
        $point = $gravity->getPoint($size = $this->getSize(), $target);

        return $point->negate();
    }

    /**
     * getWidth
     *
     * @return int
     */
    protected function getWidth()
    {
        return $this->getImage()->getWidth();
    }

    /**
     * imagick
     *
     * @return int
     */
    protected function getHeight()
    {
        return $this->getImage()->getHeight();
    }

    /**
     * imagick
     *
     * @return SizeInterface
     */
    protected function getSize()
    {
        return $this->getImage()->getSize();
    }

    /**
     * getImage
     *
     * @return void
     */
    protected function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    protected function newColor($args)
    {
        return $this->getImage()->getPalette()->getColor($args);
    }
}
