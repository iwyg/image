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

use \Thapp\Image\Filter\GdFilter;

/**
 * @class GdConvertFilter extends GdFilter
 * @see GdFilter
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class GdConvertFilter extends GdFilter
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
