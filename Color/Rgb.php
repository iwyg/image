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

use Thapp\Image\Color\Palette\Rgb as RgbPalette;
use Thapp\Image\Color\Palette\RgbPaletteInterface;

/**
 * @class Rgb
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rgb extends AbstractColor implements RgbInterface
{
    protected static $keys = [
        ColorInterface::CHANNEL_RED,
        ColorInterface::CHANNEL_GREEN,
        ColorInterface::CHANNEL_BLUE,
        ColorInterface::CHANNEL_ALPHA
    ];

    private $r;
    private $g;
    private $b;
    private $a;

    /**
     * Constructor.
     *
     * @param int   $r the read channel value.
     * @param int   $g the green channel value
     * @param int   $b the blue channel value
     * @param float $a the alpha channel value
     */
    public function __construct(array $values, RgbPaletteInterface $palette = null)
    {
        $this->setValues($values);
        $this->palette = $palette ?: new RgbPalette;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor()
    {
        return array_combine(self::keys(), [$this->r, $this->g, $this->b, $this->getAlpha()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($channel)
    {
        switch ($channel) {
            case self::CHANNEL_RED:
                return $this->r;
            case self::CHANNEL_GREEN:
                return $this->g;
            case self::CHANNEL_BLUE:
                return $this->b;
            case self::CHANNEL_ALPHA:
                return $this->getAlpha();
            default:
                break;
        }

        throw new \InvalidArgumentException('Undefined color.');
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAsString()
    {
        if (null !== $this->a && 1.0 > $this->a) {
            return sprintf('rgba(%s,%s,%s,%s)', $this->r, $this->g, $this->b, $this->a);
        }

        return sprintf('rgb(%s,%s,%s)', $this->r, $this->g, $this->b);
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
     * Sets the initial values.
     *
     * @param array $values
     * @throws \InvalidArgumentException if input values are invalid.
     *
     * @return void
     */
    private function setValues(array $values)
    {
        if (3 > ($count = count($values)) || 4 < $count) {
            throw new \InvalidArgumentException('Invalid RGB values.');
        }

        if (4 === $count) {
            $a = array_pop($values);
            $this->a = null !== $a ? (float)max(0, min(1, $a)) : null;
        }

        list ($this->r, $this->g, $this->b) = array_map(function ($color) {
            return (int)max(0, min(255, $color));
        }, array_values($values));
    }
}
