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

use Thapp\Image\Color\Palette\Cmyk as CmykPalette;
use Thapp\Image\Color\Palette\CmykPaletteInterface;

/**
 * @class Cmyk
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Cmyk extends AbstractColor implements CmykInterface
{
    protected static $keys = [
        ColorInterface::CHANNEL_CYAN,
        ColorInterface::CHANNEL_MAGENTA,
        ColorInterface::CHANNEL_YELLOW,
        ColorInterface::CHANNEL_KEY
    ];

    private $c;
    private $m;
    private $y;
    private $k;

    /**
     * Constructor.
     *
     * @param array $values
     * @param CmykPaletteInterface $palette
     */
    public function __construct(array $values, CmykPaletteInterface $palette = null)
    {
        $this->setValues($values);
        $this->palette = $palette ?: new CmykPalette;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor()
    {
        return array_combine(self::keys(), [$this->c, $this->m, $this->y, $this->k]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAsString()
    {
        return sprintf('cmyk(%d%%,%d%%,%d%%,%d%%)', $this->c, $this->m, $this->y, $this->k);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($channel)
    {
        switch ($channel) {
            case self::CHANNEL_CYAN:
                return $this->getCyan();
            case self::CHANNEL_MAGENTA:
                return $this->getMagenta();
            case self::CHANNEL_YELLOW:
                return $this->getYellow();
            case self::CHANNEL_KEY:
                return $this->getKey();
            default:
                break;
        }

        throw new \InvalidArgumentException('Undefined color.');
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException everytime it is called.
     */
    public function getAlpha()
    {
        throw new \LogicException('Alpha is unsuported on Cmyk colors.');
    }

    /**
     * {@inheritdoc}
     */
    public function getCyan()
    {
        return $this->c;
    }

    /**
     * {@inheritdoc}
     */
    public function getMagenta()
    {
        return $this->m;
    }

    /**
     * {@inheritdoc}
     */
    public function getYellow()
    {
        return $this->y;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->k;
    }

    /**
     * Set the initial values.
     *
     * @param array $values
     * @throws \InvalidArgumentException if input values are invalid.
     *
     * @return void
     */
    protected function setValues(array $values)
    {
        if (4 !== count($values)) {
            throw new \InvalidArgumentException('Invalid CMYK values.');
        }

        list($this->c, $this->m, $this->y, $this->k) = array_map(function ($color) {
            return (float)min(100, max(0, $color));
        }, array_values($values));
    }
}
