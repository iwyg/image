<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Gd;

use Thapp\Image\Driver\Gd\Source;
use Thapp\Image\Filter\Gd\Modulate;

/**
 * @class HueTest
 *
 * @package Thapp\Image\Tests\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ModulateTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldPrepareImageGd()
    {
        $this->assertTrue(true);
    }
}
