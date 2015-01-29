<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Imagick;

use Imagick;
use Thapp\Image\Driver\Imagick\Frames;
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
    public function coalesceShouldReturnSelf()
    {
        $frames = new Frames($this->mockImage($im = $this->newImagick(0)));
        $this->assertSame($frames, $frames->coalesce());

        $frames = new Frames($this->mockImage($im = $this->newImagick(12)));
        $this->assertSame($frames, $frames->coalesce());
    }

    /** @test */
    public function itShouldBeCountable()
    {
        $frames = new Frames($this->mockImage($im = $this->newImagick(0)));
        $this->assertSame(0, count($frames));
        $im->destroy();

        $frames = new Frames($this->mockImage($im = $this->newImagick(22)));
        $this->assertSame(22, count($frames));
        $im->destroy();
    }

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
        $mock = $this->getMockBuilder('Thapp\Image\Driver\Imagick\Image')
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $imagick) {
            $mock->method('getImagick')->willReturn($imagick);
        }

        $gravity = $this->getMockBuilder('Thapp\Image\Metrics\Gravity')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('getPalette')->willReturn($this->getMock('Thapp\Image\Color\Palette\Rgb'));
        $mock->method('getGravity')->willReturn($gravity);

        return $mock;
    }
}
