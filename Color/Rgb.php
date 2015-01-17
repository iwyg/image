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
 * @class Rgb
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rgb implements ColorInterface
{
    private $r;
    private $g;
    private $b;
    private $a;

    public function __construct($r, $g, $b, $a = null)
    {
        $this->r = (int)$r;
        $this->g = (int)$g;
        $this->b = (int)$b;
        $this->a = (float)$a;
    }

    public function toHex()
    {
        return new Hex(dechex($this->r).dechex($this->g).dechex($this->b));
    }

    public function toRgb()
    {
        return clone $this;
    }

    public function getRed()
    {
        return $this->r;
    }

    public function getGreen()
    {
        return $this->g;
    }

    public function getBlue()
    {
        return $this->b;
    }

    public function getAlpha()
    {
        return $this->b;
    }

    public function __toString()
    {
        if (null !== $this->a) {
            return sprintf('rgba(%s, %s, %s, %s)', $this->r, $this->g, $this->b, $this->a);
        }

        return sprintf('rgb(%s, %s, %s)', $this->r, $this->g, $this->b);
    }
}
