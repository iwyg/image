<?php

/*
 * This File is part of the Thapp\Image\Tests\Color package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Color;

use Thapp\Image\Color\Rgb;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RgbTest extends ColorTest
{
    /** @test */
    public function rgbConversionShouldReturnNewObject()
    {
        $color = new Rgb(255, 255, 255);
        $nc = $color->toRgb();

        $this->assertInstanceof('Thapp\Image\Color\Rgb', $nc);

        $this->assertFalse($nc === $color);
        $this->assertSame('rgb(255, 255, 255)',  (string)$nc);
    }

    /** @test */
    public function hexConversionShouldReturnNewObject()
    {
        $color = new Rgb(255, 255, 255);
        $nc = $color->toHex();

        $this->assertInstanceof('Thapp\Image\Color\Hex', $nc);

        $this->assertFalse($nc === $color);
        $this->assertSame('#ffffff',  (string)$nc);
    }

    /** @test */
    public function itShouldGetChannels()
    {
        $color = new Rgb(255, 0, 255);

        $this->assertSame(255, $color->getRed());
        $this->assertSame(0, $color->getGreen());
        $this->assertSame(255, $color->getBlue());
        $this->assertSame(1.0, $color->getAlpha());
    }

    protected function newColor($r, $g, $b, $a = null)
    {
        return new Rgb($r, $g, $b, $a);
    }
}
