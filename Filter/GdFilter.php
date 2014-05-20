<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

/**
 * @abstract class GdFilter extends AbstractFilter
 * @see AbstractFilter
 * @abstract
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class GdFilter extends AbstractFilter
{
    /**
     * driverType
     *
     * @var string
     */
    protected static $driverType = 'gd';
}
