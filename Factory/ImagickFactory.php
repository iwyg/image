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
use \Thapp\Image\Driver\ImagickDriver;

/**
 * @class ImagickFactory
 * @package Thapp\Image\Factory
 * @version $Id$
 */
class ImagickFactory extends AbstractFactory
{
    /**
     * createProcessor
     *
     * @access protected
     * @return mixed
     */
    protected function createProcessor()
    {
        return new Processor(new ImagickDriver($this->loader), $this->writer);
    }
}
