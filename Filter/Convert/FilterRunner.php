<?php

/**
 * This File is part of the Thapp\Image\Filter\Convert package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Convert;

/**
 * @class FilterRunner
 * @package Thapp\Image\Filter\Convert
 * @version $Id$
 */
trait FilterRunner
{
    public function runConvert()
    {
        $type = $this->getOption('f', 'jpg');

        $this->driver->setOutputType($type);
    }
}
