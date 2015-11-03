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

/**
 * @interface ColorInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ColorInterface
{
    const CHANNEL_RED      = 'red';
    const CHANNEL_GREEN    = 'green';
    const CHANNEL_BLUE     = 'blue';

    const CHANNEL_CYAN     = 'cyan';
    const CHANNEL_MAGENTA  = 'magenta';
    const CHANNEL_YELLOW   = 'yellow';
    const CHANNEL_KEY      = 'key';

    const CHANNEL_GRAY     = 'gray';
    const CHANNEL_ALPHA    = 'alpha';

    /**
     * Get the color values as array.
     *
     * @return array
     */
    public function getColor();

    /**
     * Get a colro value by attribute
     *
     * @param int $channel
     *
     * @return mixed the color attribute value.
     */
    public function getValue($channel);

    /**
     * Get the color as string.
     *
     * @return string
     */
    public function getColorAsString();

    /**
     * Get the alpha value.
     *
     * @return float
     */
    public function getAlpha();

    /**
     * Get the color palette.
     *
     * @return Thapp\Image\Color\Palette\PaletteInterface
     */
    public function getPalette();

    /**
     * Get the color as string representation
     *
     * @see ColorInterface::getColorAsString()
     *
     * @return string
     */
    public function __toString();
}
