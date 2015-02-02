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

use Thapp\Image\Color\Parser;
use Thapp\Image\Color\Rgb as RgbColor;
use Thapp\Image\Color\ColorInterface;

/**
 * @class Rgb
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rgb extends AbstractPalette implements RgbPaletteInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return RgbColor::keys();
    }

    /**
     * {@inheritdoc}
     */
    protected function createColor(array $colors)
    {
        return new RgbColor($colors, $this);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseInput($input)
    {
        return Parser::toRgb($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndex(array $colors)
    {
        list($r, $g, $b, $a) = $colors;

        return sprintf('rgba(%d,%d,%d,%d)', $r, $g, $b, $a);
    }
}
