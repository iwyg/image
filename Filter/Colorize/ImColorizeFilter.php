<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Colorize;

use \Thapp\Image\Filter\ImFilter;

/**
 * @class ImColorizeFilter
 * @package Thapp\Image
 * @version $Id$
 */
class ImColorizeFilter extends ImFilter
{
    protected $availableOptions = ['c'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return ['( +clone -fill rgb(%s) -colorize 100 ) -compose Colorize -composite' => [
                implode(',', $this->hexToRgb($this->getOption('c')))
            ]
        ];
    }
}
