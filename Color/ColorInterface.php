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

/**
 * @interface ColorInterface
 *
 * @package Thapp\Image\Color
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
     * getColor
     *
     * @return array
     */
    public function getColor();

    /**
     * valueColor
     *
     * @param mixed $channel
     *
     * @return mixed
     */
    public function getValue($channel);

    /**
     * getColorAsString
     *
     * @return void
     */
    public function getColorAsString();

    /**
     * getAlpha
     *
     * @return float
     */
    public function getAlpha();

    /**
     * getPalette
     *
     * @return Thapp\Image\Color\Palette\PaletteInterface
     */
    public function getPalette();

    /**
     * Get the color as string representation
     *
     * @return string
     */
    public function __toString();
}
