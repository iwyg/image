<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color\Palette;

/**
 * @interface PaletteInterface
 *
 * @package Thapp\Image\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PaletteInterface
{
    /**
     * Gets a color object.
     *
     * @param mixed $color the color value
     *
     * @return Thapp\Image\Color\ColorInterface
     */
    public function getColor($color);

    /**
     * Get the color channel definition as array
     *
     * @return array
     */
    public function getDefinition();
}
