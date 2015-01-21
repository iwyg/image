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
    /**
     * hex
     *
     * @var string
     */
    private $hex;

    /**
     * Constructor.
     *
     * @param string $hex
     */
    public function __construct($hex)
    {
        if (!Parser::isHex($color = ltrim($hex, '#'))) {
            throw new \InvalidArgumentException('invalid hex color '. $color);
        }

        $this->hex = ltrim(Parser::normalize($color), '#');
    }

    /**
     * {@inheritdoc}
     */
    public function toHex()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toRgb()
    {
        list ($r, $g, $b) = Parser::hexToRgb($this->hex);

        return new Rgb($r, $g, $b);
    }

    /**
     * {@inheritdoc}
     */
    public function getRed()
    {
        return hexdec(substr($this->hex, 0, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function getGreen()
    {
        return hexdec(substr($this->hex, 2, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlue()
    {
        return hexdec(substr($this->hex, 4, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlpha()
    {
        return 1.0;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '#'.$this->hex;
    }
}
