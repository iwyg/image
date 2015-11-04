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
 * @interface PointInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PointInterface
{
    /**
     * Get the X coordinate of the point.
     *
     * @return int
     */
    public function getX();

    /**
     * Get the Y coordinate of the point.
     *
     * @return int
     */
    public function getY();

    /**
     * Check if that point exists within a given Size.
     *
     * @param SizeInterface $box
     *
     * @return bool
     */
    public function isIn(SizeInterface $box);

    /**
     * Negates the coordinates.
     *
     * This method MUST return a new instance of PointInterface.
     *
     * @return PointInterface
     */
    public function negate();

    /**
     * Returns a PointInterface instance with positive x and y coordinates.
     *
     * This method MUST return a new instance of PointInterface.
     *
     * @return PointInterface
     */
    public function abs();
}
