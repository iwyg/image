<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter\Gmagick;

use Gmagick;
use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Driver\Gmagick\Source;
use Thapp\Image\Filter\Gmagick\Colorize;
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

    protected function getGmagick($w, $h)
    {
        $gmagick = new Gmagick;
        $gmagick->newImage($w, $h, 'none');

        return $gmagick;
    }

    protected function newColorize()
    {
        return new Colorize($this->mockColor());
    }

    protected function newImage()
    {
        $image = $this->getMockBuilder('Thapp\Image\Driver\Gmagick\Image')
            ->disableOriginalConstructor()
            ->getMock();

        $image->method('getGmagick')->willReturn($this->getGmagick(100, 100));

        return $image;
    }

    protected function setUp()
    {
        $this->skipIfGmagick();
    }
}
