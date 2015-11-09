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

/**
 * @interface CmykPaletteInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CmykPaletteInterface extends PaletteInterface
{
    const P_TYPE = self::PALETTE_CMYK;
}