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
use Thapp\Image\Color\Cmyk as CmykColor;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Profile\Profile;
use Thapp\Image\Color\Profile\ProfileInterface;

/**
 * @class Cmyk
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Cmyk extends AbstractPalette implements CmykPaletteInterface
{
    /** @var ProfileInterface */
    private static $defaultProfile;

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return CmykColor::keys();
    }

    /**
     * {@inheritdoc}
     */
    public function getConstant()
    {
        return self::PALETTE_CMYK;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultProfile()
    {
        if (null === static::$defaultProfile) {
            static::$defaultProfile = new Profile(
                'icc',
                realpath(ProfileInterface::RESOURCE_PATH.'/adobe/CMYK/USWebUncoated.icc')
            );
        }

        return static::$defaultProfile;
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
     * {@inheritdoc}
     */
    protected function getIndex(array $colors)
    {
        list($c, $m, $y, $k) = $colors;

        return sprintf('cmyk(%s,%s,%s,%s)', $c, $m, $y, $k);
    }
}
