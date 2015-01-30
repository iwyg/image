<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Gmagick;

use Thapp\Image\Filter\Gmagick\Modulate;
use Thapp\Image\Tests\Filter\AbstractMagickModulateTest;

/**
 * @class HueTest
 *
 * @package Thapp\Image\Tests\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ModulateTest extends AbstractMagickModulateTest
{
    protected function newModulate($b, $s, $h)
    {
        return new Modulate($b, $s, $h);
    }

    protected function prepareMagick($image)
    {
        $image->method('getGmagick')->willReturn($imagick = $this->mockGmagick());

        return $imagick;
    }

    protected function mockFrames($image)
    {
        $f = $this->getMockBuilder('Thapp\Image\Driver\Gmagick\Frames')
            ->disableOriginalConstructor()
            ->getMock();
        $f->method('coalesce')->willReturn([$image]);

        return $f;
    }

    protected function mockGmagick()
    {
        return $this->getMockBuilder('Gmagick')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockImage()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\Gmagick\Image')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function setUp()
    {
        if (!class_exists('Gmagick')) {
            $this->markTestSkipped('Gmagick extension not installed');
        }
    }
}
