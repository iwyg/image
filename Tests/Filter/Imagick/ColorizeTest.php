<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Imagick;

use Imagick;
use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Driver\Imagick\Source;
use Thapp\Image\Filter\Imagick\Colorize;
use Thapp\Image\Tests\Filter\ColorizeTest as AbstractColorizeTest;

/**
 * @class HueTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ColorizeTest extends AbstractColorizeTest
{
    protected function expectation($image)
    {
        $image->expects($this->once())->method('hasFrames')->willReturn(false);
    }

    protected function getImagick($w, $h)
    {
        $imagick = new Imagick();
        $imagick->newImage($w, $h, 'none');

        return $imagick;
    }

    protected function newImage()
    {
        $image = $this->getMockBuilder('Thapp\Image\Driver\Imagick\Image')
            ->disableOriginalConstructor()
            ->getMock();

        $image->method('getImagick')->willReturn($this->getImagick(100, 100));

        return $image;
    }

    protected function newColorize()
    {
        return new Colorize($color = $this->mockColor());
    }

    protected function setUp()
    {
        $this->skipIfImagick();
    }
}
