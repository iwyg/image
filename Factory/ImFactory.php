<?php

/**
 * This File is part of the Thapp\Image\Factory package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Factory;

use \Thapp\Image\Processor;
use \Thapp\Image\Driver\ImDriver;

/**
 * @class ImFactory
 * @package Thapp\Image\Factory
 * @version $Id$
 */
class ImFactory extends AbstractFactory
{
    /**
     * loaderInstantiator
     *
     * @var callable
     */
    protected static $loaderInstantiator;

    /**
     * writerInstantiator
     *
     * @var callable
     */
    protected static $writerInstantiator;

    /**
     * cacheInstantiator
     *
     * @var callable
     */
    protected static $cacheInstantiator;

    /**
     * createProcessor
     *
     * @access protected
     * @return \Thamm\Image\ProcessorInterface
     */
    protected function createProcessor()
    {
        return new Processor(new ImDriver($this->loader), $this->writer, $this->cache);
    }
}
