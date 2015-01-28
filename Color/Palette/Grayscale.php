<?php

/*
 * This File is part of the Thapp\Image\Palette package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color\Palette;

use Thapp\Image\Color\Parser;
use Thapp\Image\Color\Grayscale as GrayscaleColor;

/**
 * @class Greyscale
 *
 * @package Thapp\Image\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends AbstractPalette implements GrayscalePaletteInterface
{
    /**
     * getDefinition
     *
     * @return array
     */
    public function getDefinition()
    {
        return GrayscaleColor::keys();
    }

    /**
     * {@inheritdoc}
     */
    protected function createColor(array $colors)
    {
        return new GrayscaleColor($colors, $this);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseInput($input)
    {
        return Parser::toGrayscale($input);
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndex(array $colors)
    {
        list($g, $a) = $colors;

        return sprintf('gray(%d,%d)', $g, $a);
    }
}
