<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Overlay;

use Thapp\Image\Filter\GdFilter;

/**
 * @class GdOvlyFilter
 * @package Thapp\Image
 * @version $Id$
 */
class GdOvlyFilter extends GdFilter
{
    protected $availableOptions = ['c', 'a'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        list($r, $g, $b) = $this->hexToRgb($this->getOption('c', 'fff'));
        $this->createOverlay($r, $g, $b, (float)$this->getOption('a', '0.5'));
    }

    /**
     * Create the color overlay
     *
     * @param string $r Red channel
     * @param string $g Green channel
     * @param string $b Blue channel
     * @param float  $alpha alpah value as float (0.0 - 1.0)
     *
     * @return void
     */
    private function createOverlay($r, $g, $b, $alpha)
    {
        extract($this->driver->getTargetSize());

        imagealphablending($image = $this->driver->getResource(), true);

        imagefilledrectangle(
            $image,
            0,
            0,
            $width,
            $height,
            imagecolorallocatealpha($image, $r, $g, $b, (int)(127 * 0.5))
        );
    }
}
