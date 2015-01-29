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

use Thapp\Image\Color\Grayscale;
use Thapp\Image\Color\ColorInterface;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class GrayscaleTest extends ColorTest
{
    /** @test */
    public function itShouldThrowOnInvalidValues()
    {
        try {
            new Grayscale([255, 255, 255]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid Grayscale values.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldGetVlues()
    {
        $color = new Grayscale([255, 0.5]);

        $this->assertSame(255, $color->getValue(ColorInterface::CHANNEL_GRAY));
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
            ColorInterface::CHANNEL_GRAY => 50,
            ColorInterface::CHANNEL_ALPHA => 1.0,
        ];

        $color = new Grayscale([50]);
        $this->assertSame($expected, $color->getColor());
    }

    /** @test */
    public function itShouldGetPalette()
    {
        $color = new Grayscale([50]);
        $this->assertInstanceOf('Thapp\Image\Color\Palette\GrayscalePaletteInterface', $color->getPalette());
    }

    /** @test */
    public function itShouldGetColors()
    {
        $color = new Grayscale([50]);

        $this->assertSame(50, $color->getGray());
        $this->assertSame(1.0, $color->getAlpha());
    }

    /** @test */
    public function itShouldBeStringable()
    {
        $color = new Grayscale([50, 0.1]);

        $this->assertSame('rgba(50,50,50,0.1)', (string)$color);
    }

    protected function newColor($r, $g, $b, $a = null)
    {
    }
}
