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
 * @class AbstractPalette
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractPalette implements PaletteInterface
{
    /**
     * array
     *
     * @var mixed
     */
    protected static $colors;

    /**
     * {@inheritdoc}
     */
    public function getColor($color)
    {
        if (false !== ($cstring = is_string($color)) && isset(static::$colors[$color])) {
            return static::$colors[$color];
        }

        return $this->ensureColor($color, $cstring ? $color : null);
    }

    /**
     * Create and cache colors
     *
     * @param mixed $input
     * @param boolean $color
     *
     * @return Thapp\Image\Color\ColorInterface
     */
    protected function ensureColor($input, $color = null)
    {
        $index = $this->getIndex($colors = $this->parseInput($input));

        if (!isset(static::$colors[$index])) {
            static::$colors[$index] = $this->createColor($colors);

            if (null !== $color && $color !== $index) {
                static::$colors[$color] =& static::$colors[$index];
            }
        }

        return static::$colors[$index];
    }

    /**
     * Creates a new color.
     *
     * @param array $colors the color values
     *
     * @return Thapp\Image\Color\ColorInterface
     */
    abstract protected function createColor(array $colors);

    /**
     * Get the cache index for given color values.
     *
     * @param array $colors The color values
     *
     * @return string
     */
    abstract protected function getIndex(array $colors);

    /**
     * Parses the input value passed to PaletteInterface::getColor()
     *
     * @param mixed $color
     *
     * @return array
     */
    abstract protected function parseInput($color);
}
