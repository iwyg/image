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

use Thapp\Image\Color\Cmyk;
use Thapp\Image\Color\ColorInterface;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CmykTest extends ColorTest
{
    /** @test */
    public function itShouldThrowOnInvalidValues()
    {
        try {
            new Cmyk([0]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid CMYK values.', $e->getMessage());
        }

        try {
            new Cmyk([0, 0, 0, 0, 0]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid CMYK values.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowOnGettingItsAlphaChannel()
    {
        $color = $this->newColor(0, 50, 55, 0);
        try {
            $color->getAlpha();
        } catch (\LogicException $e) {
            $this->assertSame('Alpha is unsuported on Cmyk colors.', $e->getMessage());
            return;
        }
        $this->fail();
    }

    /** @test */
    public function itShouldGetVlues()
    {
        $color = $this->newColor(0, 50, 55, 0);

        $this->assertSame(0.0, $color->getValue(ColorInterface::CHANNEL_CYAN));
        $this->assertSame(50.0, $color->getValue(ColorInterface::CHANNEL_MAGENTA));
        $this->assertSame(55.0, $color->getValue(ColorInterface::CHANNEL_YELLOW));
        $this->assertSame(0.0, $color->getValue(ColorInterface::CHANNEL_KEY));

        try {
            $color->getValue(ColorInterface::CHANNEL_RED);
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
            ColorInterface::CHANNEL_CYAN => 0.0,
            ColorInterface::CHANNEL_MAGENTA => 50.0,
            ColorInterface::CHANNEL_YELLOW => 55.0,
            ColorInterface::CHANNEL_KEY => 0.0,
        ];

        $color = $this->newColor(0, 50, 55, 0);
        $this->assertSame($expected, $color->getColor());
    }

    /** @test */
    public function itShouldGetColors()
    {
        $color = $this->newColor(0, 50, 55, 0);

        $this->assertSame(0.0, $color->getCyan());
        $this->assertSame(50.0, $color->getMagenta());
        $this->assertSame(55.0, $color->getYellow());
        $this->assertSame(0.0, $color->getKey());
    }

    /** @test */
    public function itShouldBeStringable()
    {
        $color = new Cmyk([0, 50, 50, 0]);
        $this->assertSame('cmyk(0%,50%,50%,0%)', (string)$color);
    }

    /** @test */
    public function itShouldGetPalette()
    {
        $color = $this->newColor(0, 0, 100, 0);
        $this->assertInstanceOf('Thapp\Image\Color\Palette\CmykPaletteInterface', $color->getPalette());
    }

    protected function newColor($c, $m, $y, $k = null)
    {
        return new Cmyk([$c, $m, $y, $k]);
    }
}
