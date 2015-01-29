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
use Thapp\Image\Color\ColorInterface;

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
    public function itShouldThrowOnInvalidValues()
    {
        try {
            new Rgb([255]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid RGB values.', $e->getMessage());
        }

        try {
            new Rgb([255, 255, 255, 255, 255]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid RGB values.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldGetVlues()
    {
        $color = $this->newColor(255, 0, 127, 0.5);

        $this->assertSame(255, $color->getValue(ColorInterface::CHANNEL_RED));
        $this->assertSame(0, $color->getValue(ColorInterface::CHANNEL_GREEN));
        $this->assertSame(127, $color->getValue(ColorInterface::CHANNEL_BLUE));
        $this->assertSame(0.5, $color->getValue(ColorInterface::CHANNEL_ALPHA));

        try {
            $color->getValue(ColorInterface::CHANNEL_MAGENTA);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Undefined color.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldGetColorAsArray()
    {
        $expected = [
            ColorInterface::CHANNEL_RED => 255,
            ColorInterface::CHANNEL_GREEN => 0,
            ColorInterface::CHANNEL_BLUE => 127,
            ColorInterface::CHANNEL_ALPHA => 0.5,
        ];

        $color = $this->newColor(255, 0, 127, 0.5);
        $this->assertSame($expected, $color->getColor());
    }

    /** @test */
    public function itShouldGetColors()
    {
        $color = $this->newColor(255, 0, 255);

        $this->assertSame(255, $color->getRed());
        $this->assertSame(0, $color->getGreen());
        $this->assertSame(255, $color->getBlue());
        //$this->assertSame(1.0, $color->getAlpha());
    }

    /** @test */
    public function itShouldGetPalette()
    {
        $color = $this->newColor(255, 0, 255);
        $this->assertInstanceOf('Thapp\Image\Color\Palette\RgbPaletteInterface', $color->getPalette());
    }

    /** @test */
    public function itShouldBeStringable()
    {
        $color = new Rgb([255, 255, 0]);

        $this->assertSame('rgb(255,255,0)', (string)$color);

        $color = new Rgb([255, 255, 0, 0.5]);

        $this->assertSame('rgba(255,255,0,0.5)', (string)$color);
    }

    protected function newColor($r, $g, $b, $a = null)
    {
        return new Rgb([$r, $g, $b, $a]);
    }
}
