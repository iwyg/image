<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gd;

use Imagick;
use Thapp\Image\Driver\Gd\Frames;
use Thapp\Image\Tests\Driver\FramesTest as FTest;

/**
 * @class FramesTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FramesTest extends FTest
{
    /** @test */
    public function itIsExpectedThat()
    {
        $this->assertInstanceof('Thapp\Image\Driver\FramesInterface', new Frames($this->mockImage()));
    }

    /** @test */
    public function itShouldCountOneFrame()
    {
        $this->assertSame(1, (new Frames($this->mockImage()))->count());
    }

    /** @test */
    public function coalesceShouldAlwaysReturnSelf()
    {
        $frames = new Frames($image = $this->mockImage());
        $this->assertSame($frames, $frames->coalesce());
    }

    /** @test */
    public function itShouldAlwaysReturnImage()
    {
        $frames = new Frames($image = $this->mockImage());

        foreach ($frames as $frame) {
            $this->assertSame($image, $frame);
        }

        foreach ($frames->coalesce() as $frame) {
            $this->assertSame($image, $frame);
        }
    }

    /** @test */
    public function itShouldHaveArrayAccess()
    {
        $frames = new Frames($image = $this->mockImage());

        $this->assertFalse(isset($frames[0]));
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function setShouldThrowException()
    {
        $frames = new Frames($image = $this->mockImage());
        $frames->set(0, $this->mockImage());
    }

    /** @test */
    public function mergeShouldReturnNull()
    {
        $frames = new Frames($image = $this->mockImage());

        $this->assertNull($frames->merge());
    }

    /** @test */
    public function deleteShouldReturnNull()
    {
        $frames = new Frames($image = $this->mockImage());

        $this->assertNull($frames->remove(0));
    }

    ///** @test */
    //public function coalesceShouldReturnSelf()
    //{
    //    $frames = new Frames($this->mockImage($im = $this->newImagick(0)));
    //    $this->assertSame($frames, $frames->coalesce());

    //    $frames = new Frames($this->mockImage($im = $this->newImagick(12)));
    //    $this->assertSame($frames, $frames->coalesce());
    //}

    ///** @test */
    //public function itShouldBeCountable()
    //{
    //    $frames = new Frames($this->mockImage($im = $this->newImagick(0)));
    //    $this->assertSame(0, count($frames));
    //    $im->destroy();

    //    $frames = new Frames($this->mockImage($im = $this->newImagick(22)));
    //    $this->assertSame(22, count($frames));
    //    $im->destroy();
    //}

    protected function newImagick($count = 0) {
        $im = new Imagick();

        while (0 < $count) {
            $im->newImage(1, 1, 'none');
            $count--;
        }

        return $im;
    }

    protected function mockImage(Imagick $imagick = null)
    {
        $mock = $this->getMockBuilder('Thapp\Image\Driver\Gd\Image')
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $imagick) {
            $mock->method('getImagick')->willReturn($imagick);
        }

        $gravity = $this->getMockBuilder('Thapp\Image\Geometry\Gravity')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getPalette')->willReturn($this->getMock('Thapp\Image\Color\Palette\Rgb'));
        $mock->method('getGravity')->willReturn($gravity);

        return $mock;
    }

    protected function setUp()
    {
        $this->skipIfImagick();
    }
}
