<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter;

use Thapp\Image\Tests\TestHelperTrait;

/**
 * @class HueTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ColorizeTest extends \PHPUnit_Framework_TestCase
{
    use TestHelperTrait;

    /** @test */
    public function itIsExpectedThat()
    {
        $image = $this->newImage();
        $image->expects($this->once())->method('getWidth')->willReturn(100);
        $image->expects($this->once())->method('getHeight')->willReturn(100);

        $this->expectation($image);

        $filter = $this->newColorize();
        $filter->apply($image);
    }

    abstract protected function expectation($image);

    protected function mockColor()
    {
        $color = $this->getMockBuilder('Thapp\Image\Color\ColorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $color->method('__toString')->willReturn('#ff00ff');

        return $color;
    }

    abstract protected function newImage();

    abstract protected function newColorize();
}
