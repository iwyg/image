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
 * @class SizeTest
 *
 * @package Thapp\Image\Tests\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SizeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Geometry\SizeInterface', new Size(100, 100));
    }

    /**
     * @test
     * @dataProvider ratioProvider
     */
    public function itShouldCalcRatio($w, $h, $r)
    {
        $box = new Size($w, $h);
        $this->assertEquals($r, $box->getRatio());
    }

    /** @test */
    public function itShouldGetSize()
    {
        $box = new Size('50', 100);

        $this->assertEquals(50, $box->getWidth());
        $this->assertEquals(100, $box->getHeight());
    }

    /**
     * @test
     * @dataProvider scaleProvider
     */
    public function itShouldScale($w, $h, $perc, $sw, $sh)
    {
        $box = new Size($w, $h);

        $b = $box->scale($perc);
        $this->assertEquals($sw, $b->getWidth());
        $this->assertEquals($sh, $b->getHeight());
    }

    /** @test */
    public function itShouldSetPixelLimit()
    {
        $box = new Size(100, 100);
        $b = $box->pixel($px = 100000);

        $w = round(sqrt($px));
        $this->assertEquals($w, $b->getWidth());
        $this->assertEquals($w, $b->getHeight());
    }

    /** @test */
    public function itShouldContainSize()
    {
        $box = new Size(100, 100);

        $this->assertFalse($box->contains(new Size(200, 100)));
        $this->assertTrue($box->contains($box));
    }

    /** @test */
    public function itShouldHavePoint()
    {
        $box = new Size(100, 100);

        $this->assertFalse($box->has(new Point(200, 100)));
        $this->assertTrue($box->has(new Point(10, 100)));
    }

    /** @test */
    public function itShouldIncreaseSizeByWidth()
    {
        $box = new Size(100, 50);

        $b = $box->increaseByWidth(200);

        $this->assertSame(300, $b->getWidth());
        $this->assertSame(150, $b->getHeight());
    }

    /** @test */
    public function itShouldIncreaseSizeByHeight()
    {
        $box = new Size(100, 50);

        $b = $box->increaseByHeight(100);

        $this->assertSame(300, $b->getWidth());
        $this->assertSame(150, $b->getHeight());
    }

    /**
     * @test
     * @dataProvider fitProvider
     */
    public function itShouldFinInSize(Size $fit, $w, $h)
    {
        $box = new Size(100, 100);

        $b = $box->fit($fit);

        $this->assertSame($w, $b->getWidth());
        $this->assertSame($h, $b->getHeight());
    }

    /** @test */
    public function itShouldFillInSize()
    {
        $box = new Size(100, 50);
        $boxB = new Size(400, 400);

        $b = $box->fill($boxB);
    }

    /** @test */
    public function itShouldRotate()
    {
        $box = new Size(100, 50);

        $b = $box->rotate(-90);
        $this->assertSame(50, $b->getWidth());
        $this->assertSame(100, $b->getHeight());
        $b = $box->rotate(90);
        $this->assertSame(50, $b->getWidth());
        $this->assertSame(100, $b->getHeight());
        $b = $box->rotate(180);
        $this->assertSame(100, $b->getWidth());
        $this->assertSame(50, $b->getHeight());
        $b = $box->rotate(45);
        $this->assertTrue(100 < $b->getWidth());
        $this->assertSame($b->getWidth(), $b->getHeight());
    }

    public function ratioProvider()
    {
        return [
            [100, 100, 1.0],
            [100, 50, 2.0],
            [50, 100, 0.5]
        ];
    }

    public function scaleProvider()
    {
        return [
            [100, 100, 200, 200, 200],
            [100, 100, 50, 50, 50],
        ];
    }

    public function fitProvider()
    {
        return [
            [new Size(100, 100), 100, 100],
            [new Size(100, 200), 100, 100],
            [new Size(200, 100), 100, 100],
            [new Size(400, 200), 200, 200],
        ];
    }
}
