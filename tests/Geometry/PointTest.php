<?php

/*
 * This File is part of the Thapp\Image\Tests\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Geometry;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;

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
        $this->assertInstanceof('Thapp\Image\Geometry\PointInterface', new Point(100, 100));
    }

    /** @test */
    public function itShouldNegatePoint()
    {
        $point = (new Point(100, -200))->negate();

        $this->assertSame(-100, $point->getX());
        $this->assertSame(200, $point->getY());
    }

    /** @test */
    public function itShouldAbsPoint()
    {
        $point = (new Point(100, -200))->abs();

        $this->assertSame(100, $point->getX());
        $this->assertSame(200, $point->getY());
    }

    /** @test */
    public function itShouldBeIn()
    {
        $point = new Point(100, 100);
        $size = new Size(100, 200);

        $this->assertTrue($point->isIn($size));
    }

    /** @test */
    public function itShouldNotBeIn()
    {
        $point = new Point(100, 100);
        $size = new Size(100, 99);

        $this->assertFalse($point->isIn($size));
    }
}
