<?php

/*
 * This File is part of the Thapp\Image\Color\Palette package
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
 * @package Thapp\Image\Color\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractPalette implements PaletteInterface
{
    protected static $colors;

    /**
     * {@inheritdoc}
     */
    public function getColor($color)
    {
        if ($cstring = is_string($color) && isset(static::$colors[$color])) {
            if (isset(static::$colors[$color])) {
                return static::$colors[$color];
            }
        }

        return $this->ensureColor($color, $cstring ? $color : null);
    }

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

    abstract protected function createColor(array $colors);

    abstract protected function getIndex(array $colors);

    abstract protected function parseInput($color);
}
