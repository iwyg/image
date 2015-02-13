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
use Thapp\Image\Color\Grayscale as GrayscaleColor;
use Thapp\Image\Color\Profile\Profile;

/**
 * @class Greyscale
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends AbstractPalette implements GrayscalePaletteInterface
{
    private static $defaultProfile;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return GrayscaleColor::keys();
    }

    /**
     * {@inheritdoc}
     */
    public function getConstant()
    {
        return self::PALETTE_GRAYSCALE;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultProfile()
    {
        if (null === static::$defaultProfile) {
            static::$defaultProfile = new Profile(
                'icc',
                realpath(__DIR__.'/../../resource/colormanagement.org/ISOcoated_v2_grey1c_bas.icc')
            );
        }

        return static::$defaultProfile;
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
