<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Geometry;

/**
 * @interface GravityInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface GravityInterface
{
    const GRAVITY_NORTHWEST = 1;
    const GRAVITY_NORTH = 2;
    const GRAVITY_NORTHEAST = 3;
    const GRAVITY_WEST = 4;
    const GRAVITY_CENTER = 5;
    const GRAVITY_EAST = 6;
    const GRAVITY_SOUTHWEST = 7;
    const GRAVITY_SOUTH = 8;
    const GRAVITY_SOUTHEAST = 9;

    /**
     * Get the mode
     *
     * @return int mode defined by one of the GravityInterface::GRAVITY_*
     * constants.
     */
    public function getMode();

    /**
     * getPoint
     *
     * @param SizeInterface $source
     * @param SizeInterface $target
     *
     * @return PointInterface
     */
    public function getPoint(SizeInterface $source, SizeInterface $target);
}
