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
use Thapp\Image\Color\Cmyk as CmykColor;
use Thapp\Image\Color\ColorInterface;

/**
 * @class Cmyk
 *
 * @package Thapp\Image\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Cmyk extends AbstractPalette implements CmykPaletteInterface
{
    public function getDefinition()
    {
        return CmykColor::keys();
    }

    /**
     * {@inheritdoc}
     */
    protected function createColor(array $colors)
    {
        return new CmykColor($colors, $this);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseInput($input)
    {
        return Parser::toCmyk($input);
    }

    /**
     * getIndex
     *
     * @param array $colors
     *
     * @return void
     */
    protected function getIndex(array $colors)
    {
        list($c, $m, $y, $k) = $colors;

        return sprintf('cmyk(%s,%s,%s,%s)', $c, $m, $y, $k);
        //var_dump($key);
        //return $key;
    }
}
