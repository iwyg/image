<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Convert;

use \Thapp\Image\Filter\ImagickFilter;

/**
 * @class ImagickConvertFilter extends ImagickFilter
 * @see ImagickFilter
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ImagickConvertFilter extends ImagickFilter
{
    use FilterRunner;

    /**
     * {@inheritdoc}
     */
    protected $availableOptions = ['f'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runConvert();
    }
}
