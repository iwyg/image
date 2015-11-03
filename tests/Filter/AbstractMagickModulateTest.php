<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter;

/**
 * @class AbstractMagickModulateTest
 *
 * @package Thapp\Image\Tests\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractMagickModulateTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldCallMudiulateOnImagick()
    {
        $image = $this->mockImage();
        $imagick = $this->prepareMagick($image);
        $image->expects($this->once())->method('hasFrames')->with()->willReturn(false);
        $imagick->expects($this->once())->method('modulateImage')->with(100, 120, 50);

        $filter = $this->newModulate(100, 120, 50);
        $filter->apply($image);
    }

    /** @test */
    public function itShouldCallMudiulateOnImagickIfItHasFrames()
    {
        $image = $this->mockImage();
        $imagick = $this->prepareMagick($image);
        $image->expects($this->once())->method('hasFrames')->with()->willReturn(true);
        $image->method('frames')->willReturn($this->mockFrames($image));
        $imagick->expects($this->once())->method('modulateImage')->with(100, 120, 50);

        $filter = $this->newModulate(100, 120, 50);
        $filter->apply($image);
    }

    abstract protected function newModulate($b, $s, $h);
    abstract protected function prepareMagick($image);
    abstract protected function mockFrames($image);
    abstract protected function mockImage();
}
