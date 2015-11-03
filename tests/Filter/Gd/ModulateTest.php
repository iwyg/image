<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Gd;

use Thapp\Image\Driver\Gd\Source;
use Thapp\Image\Filter\Gd\Modulate;

/**
 * @class HueTest
 *
 * @package Thapp\Image\Tests\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ModulateTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldPrepareImageGd()
    {
        $filter = new Modulate(100, 100, 120);
        $gd = imagecreatetruecolor(1, 1);
        $image = $this->mockImage();

        $image->method('getGd')->willReturn($gd);

        $image->expects($this->once())->method('getWidth')->willReturn(1);
        $image->expects($this->once())->method('getHeight')->willReturn(1);

        $filter->apply($image);

        imagedestroy($gd);
    }

    /** @test */
    public function itShouldDoNothingIfValuesAreDefault()
    {
        $filter = new Modulate(100, 100, 100);
        $gd = imagecreatetruecolor(1, 1);
        $image = $this->mockImage();

        $image->method('getGd')->willReturn($gd);

        $image->expects($this->once())->method('getWidth')->willReturn(1);
        $image->expects($this->once())->method('getHeight')->willReturn(1);

        $filter->apply($image);

        imagedestroy($gd);
    }

    protected function mockImage()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\Gd\Image')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
