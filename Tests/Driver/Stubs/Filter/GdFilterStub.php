<?php

/**
 * This File is part of the Thapp\Image\Tests\Driver\Stubs\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Stubs\Filter;

use \Thapp\Image\Filter\GdFilter;

/**
 * @class GdFilterStub
 * @package Thapp\Image\Tests\Driver\Stubs\Filter
 * @version $Id$
 */
class GdFilterStub extends GdFilter
{
    protected static $mockReporter;

    public function run()
    {
        if (static::$mockReporter) {
            static::$mockReporter->run('success');
        }

        static::$mockReporter = null;
    }

    public static function setMockReporter($mock)
    {
        static::$mockReporter = $mock;
    }
}
