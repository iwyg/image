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
 * @class SizeInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SizeInterface
{
    /**
     * Get the aspect ratio of the Size object.
     *
     * @return float
     */
    public function getRatio();

    /**
     * Get the width in px of the Size object.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get the height in px of the Size object.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Scales the Size object by the given percentage.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param float $perc
     *
     * @return SizeInterface
     */
    public function scale($perc);

    /**
     * Scales the Size object to a maximum pixel count
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param int $limit
     *
     * @return SizeInterface
     */
    public function pixel($limit);

    /**
     * Increases the Size object by width in px.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param int $width
     *
     * @return SizeInterface
     */
    public function increaseByWidth($width);

    /**
     * Increases the Size object by height in px.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param int $height
     *
     * @return SizeInterface
     */
    public function increaseByHeight($height);

    /**
     * sizeFromRatio
     *
     * @param int $width
     * @param int $height
     *
     * @return SizeInterface
     */
    public function getSizeFromRatio($width = 0, $height = 0);

    /**
     * contains
     *
     * @param SizeInterface $box
     * @param PointInterface $point
     *
     * @return bool
     */
    public function contains(SizeInterface $box, PointInterface $point = null);

    /**
     * Checks if given Point is within the Size object.
     *
     * @param PointInterface $point
     *
     * @return bool
     */
    public function has(PointInterface $point);

    /**
     * fill
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param SizeInterface $target
     *
     * @return SizeInterface
     */
    public function fill(SizeInterface $target);

    /**
     * Fits the origin Size object in a referenced Size object.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param SizeInterface $target
     *
     * @return SizeInterface
     */
    public function fit(SizeInterface $target);

    /**
     * Rotates the quadrant.
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param float $deg
     *
     * @return SizeInterface
     */
    public function rotate($deg);
}
