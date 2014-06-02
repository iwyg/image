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
 * @class ImagickFactory extends AbstractFactory
 * @see AbstractFactory
 *
 * @package Thapp\Image\Factory
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ImagickFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createProcessor()
    {
        return new Processor(new ImagickDriver($this->loader), $this->writer);
    }
}
