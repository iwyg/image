<?php

/*
 * This File is part of the Thapp\Image\Color package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @interface ColorInterface
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ColorInterface
{
    /**
     * toHex
     *
     * @return ColorInterface
     */
    public function toHex();

    /**
     * toRgb
     *
     * @return ColorInterface
     */
    public function toRgb();

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

    /**
     * Get the color as string representation
     *
     * @return string
     */
    public function __toString();
}
