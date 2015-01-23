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
    /**
     * r
     *
     * @var int
     */
    private $r;

    /**
     * g
     *
     * @var int
     */
    private $g;

    /**
     * b
     *
     * @var int
     */
    private $b;

    /**
     * a
     *
     * @var float
     */
    private $a;

    /**
     * Constructor.
     *
     * @param int   $r the read channel value.
     * @param int   $g the green channel value
     * @param int   $b the blue channel value
     * @param float $a the alpha channel value
     */
    public function __construct($r, $g, $b, $a = null)
    {
        $this->r = (int)$r;
        $this->g = (int)$g;
        $this->b = (int)$b;
        $this->a = null === $a ? $a : (float)min(max($a, 0), 1.0);
    }

    /**
     * {@inheritdoc}
     */
    public function toHex()
    {
        return new Hex(Parser::rgbToHex($this->r, $this->g, $this->b));
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * {@inheritdoc}
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlpha()
    {
        return null !== $this->a ? $this->a : 1.0;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (null !== $this->a) {
            return sprintf('rgba(%s,%s,%s,%s)', $this->r, $this->g, $this->b, $this->a);
        }

        return sprintf('rgb(%s,%s,%s)', $this->r, $this->g, $this->b);
    }
}
