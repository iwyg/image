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

use Thapp\Image\Filter\AutoRotate;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class AutorotateTest
 *
 * @package Thapp\Image\Tests\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AutoRotateTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldRotate180Degree()
    {
        $filter = new AutoRotate;
        $image = $this->mockImage(ImageInterface::ORIENT_BOTTOMRIGHT);
        $image->edit()->expects($this->once())->method('rotate')->with(180);

        $filter->apply($image);
    }

    /** @test */
    public function itShouldRotate900Degree()
    {
        $filter = new AutoRotate;
        $image = $this->mockImage(ImageInterface::ORIENT_RIGHTTOP);
        $image->edit()->expects($this->once())->method('rotate')->with(90);

        $filter->apply($image);
    }

    /** @test */
    public function itShouldRotateNegative900Degree()
    {
        $filter = new AutoRotate;
        $image = $this->mockImage(ImageInterface::ORIENT_LEFTBOTTOM);
        $image->edit()->expects($this->once())->method('rotate')->with(-90);

        $filter->apply($image);
    }

    /** @test */
    public function itShouldNotRoate()
    {
        $filter = new AutoRotate;
        $image = $this->mockImage(ImageInterface::ORIENT_UNDEFINED);
        $image->edit()->expects($this->exactly(0))->method('rotate');

        $filter->apply($image);

        $image = $this->mockImage(ImageInterface::ORIENT_LEFTTOP);
        $image->edit()->expects($this->exactly(0))->method('rotate');

        $filter->apply($image);

        $image = $this->mockImage(ImageInterface::ORIENT_TOPRIGHT);
        $image->edit()->expects($this->exactly(0))->method('rotate');

        $filter->apply($image);
    }

    protected function mockImage($orient = ImageInterface::ORIENT_UNDEFINED)
    {
        $image = $this->getMockBuilder('Thapp\Image\Driver\ImageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $image->method('getOrientation')->willReturn($orient);
        $image->method('edit')->willReturn($this->mockEdit());

        return $image;
    }

    protected function mockEdit()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\EditInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
