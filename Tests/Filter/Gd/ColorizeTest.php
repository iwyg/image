<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\GD;

use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Driver\Gd\Source;
use Thapp\Image\Filter\Gd\Colorize;
use Thapp\Image\Tests\Filter\ColorizeTest as AbstractColorizeTest;

/**
 * @class HueTest
 *
 * @package Thapp\Image\Tests\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ColorizeTest extends AbstractColorizeTest
{
    protected function expectation($image)
    {
    }

    protected function getGd($w, $h)
    {
        $gd = imagecreatetruecolor($w, $h);
        return $gd;
    }

    protected function newImage()
    {
        $image = $this->getMockBuilder('Thapp\Image\Driver\Gd\Image')
            ->disableOriginalConstructor()
            ->getMock();

        $image->method('getGd')->willReturn($this->getGd(100, 100));

        return $image;
    }

    protected function newColorize()
    {
        $filter = new Colorize($color = $this->mockColor());
        $color->expects($this->once())->method('getRed')->willReturn(255);
        $color->expects($this->once())->method('getBlue')->willReturn(0);
        $color->expects($this->once())->method('getGreen')->willReturn(255);

        return $filter;
    }

    protected function mockColor()
    {
        return $this->getMockBuilder('Thapp\Image\Color\Rgb')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
