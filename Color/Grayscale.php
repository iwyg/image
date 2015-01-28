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

use Thapp\Image\Color\Palette\Grayscale as GrayscalePalette;
use Thapp\Image\Color\Palette\GrayscalePaletteInterface;

/**
 * @class Greyscale
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends AbstractColor implements GrayscaleInterface
{
    protected static $keys = [
        ColorInterface::CHANNEL_GRAY,
        ColorInterface::CHANNEL_ALPHA
    ];

    private $g;
    private $a;

    /**
     * Constructor.
     *
     * @param int $grey
     * @param float $alpha
     */
    public function __construct(array $values, GrayscalePaletteInterface $palette = null)
    {
        $this->setValues(array_values($values));
        $this->palette = $palette ?: new GrayscalePalette;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor()
    {
        return array_combine(self::keys(), [$this->g, $this->getAlpha()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAsString()
    {
        return sprintf('rgba(%1$d,%1$d,%1$d,%2$d)', $this->g, $this->getAlpha());
    }

    /**
     * {@inheritdoc}
     */
    public function getGray()
    {
        return $this->g;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($channel)
    {
        if (self::CHANNEL_GRAY === $channel) {
            return $this->getGray();
        }

        if (self::CHANNEL_ALPHA === $channel) {
            return $this->getAlpha();
        }

        throw new \InvalidArgumentException;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlpha()
    {
        return null !== $this->a ? $this->a : 1.0;
    }

    protected function setValues(array $values)
    {
        if (empty($values) || 2 < count($values)) {
            throw new \InvalidArgumentException('Invalid values.');
        }

        $this->g = (int)max(0, min(255, $values[0]));
        $this->a = isset($values[1]) ? (float)max(0, min(1, $values[1])) : null;
    }
}
