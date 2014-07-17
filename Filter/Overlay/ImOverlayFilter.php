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

use \Thapp\Image\Filter\ImFilter;

/**
 * @class ImOverlayFilter
 * @package Thapp\Image
 * @version $Id$
 */
class ImOverlayFilter extends ImFilter
{
    protected $availableOptions = ['c', 'a'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return ['( +clone -fill rgb(%s) -colorize %s ) -compose Over -composite' => [
                implode(',', $this->hexToRgb($this->getOption('c'))),
                ((float)$this->getOption('a', '0.5')) * 100
            ]
        ];
    }
}
