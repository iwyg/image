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
    /** @var int */
    const GRAVITY_NORTHWEST = 1;

    /** @var int */
    const GRAVITY_NORTH     = 2;

    /** @var int */
    const GRAVITY_NORTHEAST = 3;

    /** @var int */
    const GRAVITY_WEST      = 4;

    /** @var int */
    const GRAVITY_CENTER    = 5;

    /** @var int */
    const GRAVITY_EAST      = 6;

    /** @var int */
    const GRAVITY_SOUTHWEST = 7;

    /** @var int */
    const GRAVITY_SOUTH     = 8;

    /** @var int */
    const GRAVITY_SOUTHEAST = 9;

    /**
     * Get the Gravity mode.
     *
     * @return int mode defined by one of the GravityInterface::GRAVITY_*
     * constants.
     */
    public function getMode();

    /**
     * Get the top left point of a target size from a given source size.
     *
     * @param SizeInterface $source
     * @param SizeInterface $target
     *
     * @return PointInterface
     */
    public function getPoint(SizeInterface $source, SizeInterface $target);
}
