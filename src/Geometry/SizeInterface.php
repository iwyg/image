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
     * getRatio
     *
     * @return float
     */
    public function getRatio();

    /**
     * getWidth
     *
     * @return int
     */
    public function getWidth();

    /**
     * getHeight
     *
     * @return int
     */
    public function getHeight();

    /**
     * scale
     *
     * @param int $perc
     *
     * @return SizeInterface
     */
    public function scale($perc);

    /**
     * pixel
     *
     * @param int $limit
     *
     * @return SizeInterface
     */
    public function pixel($limit);

    /**
     * increaseByWidth
     *
     * This method MUST return a new instance of SizeInterface.
     *
     * @param int $width
     *
     * @return SizeInterface
     */
    public function increaseByWidth($width);

    /**
     * increaseByHeight
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
     * has
     *
     * @param mixed $box
     * @param PointInterface $point
     *
     * @return boolean
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
     * fit
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
