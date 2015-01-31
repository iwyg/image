<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Imagick;

use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Driver\Imagick\Source;
use Thapp\Image\Filter\Imagick\Modulate;
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
    use TestHelperTrait;

    protected function newModulate($b, $s, $h)
    {
        return new Modulate($b, $s, $h);
    }

    protected function prepareMagick($image)
    {
        $image->method('getImagick')->willReturn($imagick = $this->mockImagick());

        return $imagick;
    }

    protected function mockFrames($image)
    {
        $f = $this->getMockBuilder('Thapp\Image\Driver\Imagick\Frames')
            ->disableOriginalConstructor()
            ->getMock();
        $f->method('coalesce')->willReturn([$image]);

        return $f;
    }

    protected function mockImagick()
    {
        return $this->getMockBuilder('Imagick')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockImage()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\Imagick\Image')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function setUp()
    {
        $this->skipIfImagick();
    }
}
