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
 * @class Hex
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Hex implements ColorInterface
{
    private $hex;

    public function __construct($hex)
    {
        if (!Parser::isHex($color = ltrim($hex, '#'))) {
            throw new \InvalidArgumentException('invalid hex color '. $color);
        }

        $this->hex = ltrim(Parser::normalize($color), '#');
    }

    public function toHex()
    {
        return clone $this;
    }

    public function toRgb()
    {
        list ($r, $g, $b) = Parser::hexToRgb($this->hex);

        return new Rgb($r, $g, $b);
    }

    public function getRed()
    {
        return hexdec(substr($this->hex, 0, 2));
    }

    public function getGreen()
    {
        return hexdec(substr($this->hex, 2, 2));
    }

    public function getBlue()
    {
        return hexdec(substr($this->hex, 4, 2));
    }

    public function getAlpha()
    {
        return null;
    }

    public function __toString()
    {
        return '#'.$this->hex;
    }
}
