<?php

/*
 * This File is part of the Thapp\Image\Tests\Palette package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Color\Palette;

use Thapp\Image\Color\Palette\Rgb;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RgbTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnColor()
    {
        $palette = new Rgb;
        $this->assertInstanceof('Thapp\Image\Color\ColorInterface', $palette->getColor([255,255,255]));
    }
}
