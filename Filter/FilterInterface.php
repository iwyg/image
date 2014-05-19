<?php

/**
 * This File is part of the Thapp\Image\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

use \Thapp\Image\Driver\DriverInterface;

/**
 * @class FilterInterface
 * @package Thapp\Image\Filter
 * @version $Id$
 */
interface FilterInterface
{
    public function __construct(DriverInterface $driver, $options);

    /**
     * Run the filter
     *
     * @access public
     * @return void
     */
    public function run();

    /**
     * Get a filter option.
     *
     * @param string $option option name
     * @param mixed  $default the default value to return
     * @access public
     * @return mixed
     */
    public function getOption($option, $default = null);

    /**
     * get the filter alas
     *
     * @access public
     * @return string
     */
    //public function getAlias();
}
