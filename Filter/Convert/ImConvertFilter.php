<?php

/**
 * This File is part of the Thapp\JitImage\Filter\Convert package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Convert;

use \Thapp\Image\Filter\ImagickFilter;

/**
 * @class ImagickConvFilter
 * @package Thapp\JitImage\Filter\Convert
 * @version $Id$
 */
class ImConvertFilter extends ImagickFilter
{
    use FilterRunner;

    protected $availableOptions = ['f'];

    public function run()
    {
        $this->runConvert();
    }
}
