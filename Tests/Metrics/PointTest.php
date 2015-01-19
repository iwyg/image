<?php

/*
 * This File is part of the Thapp\Image\Tests\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Metrics;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;

/**
 * @class PointTest
 *
 * @package Thapp\Image\Tests\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Metrics\PointInterface', new Point(100, 100));
    }

    /** @test */
    public function itShouldNegatePoint()
    {
        $point = new Point(100, -200);

        $point = $point->negate();

        $this->assertSame(-100, $point->getX());
        $this->assertSame(200, $point->getY());
    }
}
