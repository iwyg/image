<?php

/*
 * This File is part of the Thapp\Image\Metrics package
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
 * @package Thapp\Image\Metrics
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
     * @param mixed $width
     *
     * @return SizeInterface
     */
    public function increaseByWidth($width);

    /**
     * increaseByHeight
     *
     * @param mixed $height
     *
     * @return SizeInterface
     */
    public function increaseByHeight($height);

    /**
     * contains
     *
     * @param SizeInterface $box
     * @param PointInterface $point
     *
     * @return boolean
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
     * @param SizeInterface $target
     *
     * @return SizeInterface
     */
    public function fill(SizeInterface $target);

    /**
     * fit
     *
     * @param SizeInterface $target
     *
     * @return SizeInterface
     */
    public function fit(SizeInterface $target);

    /**
     * rotate
     *
     * @param float $deg
     *
     * @return SizeInterface
     */
    public function rotate($deg);
}
