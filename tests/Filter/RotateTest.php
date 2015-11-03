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

use Thapp\Image\Filter\Rotate;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class RotateTest
 *
 * @package Thapp\Image\Tests\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RotateTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldRotate45Degree()
    {
        $filter = new Rotate(45);
        $image = $this->mockImage();
        $image->edit()->expects($this->once())->method('rotate')->with(45, null);

        $filter->apply($image);
    }

    /** @test */
    public function itShouldRotateFrames()
    {
        $filter = new Rotate(45);
        $image = $this->mockImage(true);
        $image->edit()->expects($this->once())->method('rotate')->with(45, null);

        $filter->apply($image);
    }

    /** @test */
    public function itShouldRotate45DegreeWithColor()
    {
        $filter = new Rotate(45, $color = $this->mockColor());
        $image = $this->mockImage();
        $image->edit()->expects($this->once())->method('rotate')->with(45, $color);

        $filter->apply($image);
    }

    protected function mockColor()
    {
        $image = $this->getMockBuilder('Thapp\Image\Color\ColorInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockImage($frames = false)
    {
        $image = $this->getMockBuilder('Thapp\Image\Driver\ImageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $image->method('edit')->willReturn($this->mockEdit());
        $image->method('hasFrames')->willReturn($frames);
        $image->method('frames')->willReturn($this->mockFrames($image));

        return $image;
    }

    protected function mockEdit()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\EditInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockFrames($image)
    {
        $frames = $this->getMockBuilder('Thapp\Image\Driver\FramesInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $frames->method('coalesce')->willReturn([$image]);

        return $frames;
    }
}
