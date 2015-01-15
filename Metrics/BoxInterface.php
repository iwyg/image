<?php

/*
 * This File is part of the Thapp\Image\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Metrics;

/**
 * @class BoxInterface
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface BoxInterface
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
     * @return BoxInterface
     */
    public function scale($perc);

    /**
     * pixel
     *
     * @param int $limit
     *
     * @return BoxInterface
     */
    public function pixel($limit);

    /**
     * increaseByWidth
     *
     * @param mixed $width
     *
     * @return BoxInterface
     */
    public function increaseByWidth($width);

    /**
     * increaseByHeight
     *
     * @param mixed $height
     *
     * @return BoxInterface
     */
    public function increaseByHeight($height);

    /**
     * contains
     *
     * @param BoxInterface $box
     * @param PointInterface $point
     *
     * @return boolean
     */
    public function contains(BoxInterface $box, PointInterface $point = null);

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
     * @param BoxInterface $target
     *
     * @return BoxInterface
     */
    public function fill(BoxInterface $target);

    /**
     * fit
     *
     * @param BoxInterface $target
     *
     * @return BoxInterface
     */
    public function fit(BoxInterface $target);

    /**
     * rotate
     *
     * @param float $deg
     *
     * @return BoxInterface
     */
    public function rotate($deg);
}
