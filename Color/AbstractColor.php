<?php

/*
 * This File is part of the Thapp\Image\Color\Palette package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @class AbstractColor
 *
 * @package Thapp\Image\Color\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractColor implements ColorInterface
{
    protected static $keys = [];
    protected $palette;

    /**
     * Returns the colors' key definition
     *
     * @return array
     */
    public static function keys()
    {
        return static::$keys;
    }

    /**
     * {@inheritdoc}
     */
    public function getPalette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getColorAsString();
    }
}
