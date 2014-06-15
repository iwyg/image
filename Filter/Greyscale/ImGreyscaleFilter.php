<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Greyscale;

use \Thapp\Image\Filter\ImFilter;

/**
 * @class ImGreyscaleFilter
 * @package Thapp\Image
 * @version $Id$
 */
class ImGreyscaleFilter extends ImFilter
{
    protected $availableOptions = ['h', 's', 'b', 'c'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return [
            '-modulate %s,%s,%s' => [
                (int)$this->getOption('b', 100),
                (int)$this->getOption('s', 0),
                (int)$this->getOption('h', 100)
            ],
            '%scontrast' => [false !== (bool)$this->getOption('c', 1) ? '-' : '+'],
        ];
    }
}
