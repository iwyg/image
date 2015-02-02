<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @interface RgbInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RgbInterface
{
    /**
     * Get the red channel value
     *
     * @return int
     */
    public function getRed();

    /**
     * Get the green channel value
     *
     * @return int
     */
    public function getGreen();

    /**
     * Get the blue channel value
     *
     * @return int
     */
    public function getBlue();

    /**
     * Get the alpha channel value
     *
     * @return float
     */
    public function getAlpha();
}
