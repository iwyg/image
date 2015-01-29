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
use Thapp\Image\Color\Parser;

/**
 * @class ParserTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldConvertColors()
    {
        $channels = [$r = 255, $g = 10, $b = 21];

        $hex = Parser::rgbToHex($r, $g, $b);

        $this->assertSame('ff0a15', $hex);

        $rgb = Parser::hexToRgb($hex);

        $this->assertSame($channels, $rgb);
    }

    /** @test */
    public function itThrowOnInvalidInputType()
    {
        try {
            Parser::toRgb('cmyk(200,200,foo,bar)');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid cmyk definition "cmyk(200,200,foo,bar)".', $e->getMessage());
        }

        try {
            Parser::toRgb('rgb(200,200)');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid rgb definition "rgb(200,200)".', $e->getMessage());
        }

        try {
            Parser::toRgb('foo_bar');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid color definition "foo_bar".', $e->getMessage());
        }

        try {
            Parser::toRgb(new \stdClass);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Unsupported argument value of type object.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /**
     * @test
     * @dataProvider normalizeHexProvider
     */
    public function itShouldNormalizeHexStrings($hex, $normalized)
    {
        $this->assertEquals($normalized, Parser::normalizeHex($hex));
    }

    public function normalizeHexProvider()
    {
        return [
            ['fff', '#ffffff'],
            ['#ffffff', '#ffffff'],
            ['#fff', '#ffffff'],
            ['dff', '#ddffff'],
        ];
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionOnInvalidHexvalues()
    {
        $hex = Parser::hexToRgb('#ibdcefg');
    }

    /** @test */
    public function itShouldCompensateForHexShortNotation()
    {
        $rgb = Parser::hexToRgb('#fff');
        $this->assertSame([255, 255, 255], $rgb);
    }

    /** @test */
    public function itShouldConvertRgbStringColorToRgb()
    {
        $rgb = Parser::toRgb('rgba(255,127, 255, 0.5)');

        $this->assertInternalType('array', $rgb);
        $this->assertSame(255, $rgb[0]);
        $this->assertSame(127, $rgb[1]);
        $this->assertSame(255, $rgb[2]);
        $this->assertSame(0.5, $rgb[3]);
    }

    /** @test */
    public function itShouldConvertChannelsArrayToRgb()
    {
        $rgb = Parser::toRgb([255, 127, 255]);

        $this->assertInternalType('array', $rgb);
        $this->assertSame(255, $rgb[0]);
        $this->assertSame(127, $rgb[1]);
        $this->assertSame(255, $rgb[2]);
        $this->assertSame(1.0, $rgb[3]);
    }

    /**
     * @test
     * @dataProvider parseToRgbProvider
     */
    public function itShouldParseToRgb($color, $expected)
    {
        $this->assertEquals($expected, Parser::toRgb($color));
    }

    /**
     * @test
     * @dataProvider parseToGrayProvider
     */
    public function itShouldParseToGrayscale($color, $expected)
    {
        $this->assertEquals($expected, Parser::toGrayscale($color));
    }

    /**
     * @test
     * @dataProvider parseTo4cProvider
     */
    public function itShouldParseTo4c($color, $expected)
    {
        $this->assertEquals($expected, Parser::to4c($color));
    }

    /** @test */
    public function itShouldAlwaysReturnAlphaChannel()
    {
        $values = Parser::toRgb([0, 0, 0]);

        $this->assertSame([0, 0, 0, 1.0], $values);
    }

    /** @test */
    public function itShouldThrowOnInvalidInput()
    {
        try {
            Parser::toRgb([0, 0, 0, 0, 0]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid argument value [0,0,0,0,0].', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function parseToGrayShouldThrowOnInvalidInput()
    {
        try {
            Parser::toGrayscale('ffee7f');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid color set [255,238,127,1] for grayscale conversion.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    public function parseToRgbProvider()
    {
        return [
            [1073676288, [255, 0, 0, 0.5]],
            [16777215, [255, 255, 255, 1.0]],
            [1073676543, [255, 0, 255, 0.5]],
            ['rgba(255, 127, 0, 0.5)', [255, 127, 0, 0.5]],
            ['#ff7f00', [255, 127, 0, 1.0]],
            [[127], [127, 127, 127, 1.0]],
            [[127, 0.5], [127, 127, 127, 0.5]],
            [array_combine(Cmyk::keys(), [0, 95, 94, 0]), [255, 13, 15, 1.0]],
            ['cmyk(0, 95, 94, 0)', [255, 13, 15, 1.0]],
            ['cmyk(0, 100, 100, 0)', [255, 0, 0, 1.0]],
            ['cmyk(100, 100, 0, 0)', [0, 0, 255, 1.0]],
            ['cmyk(100, 100, 100, 0)', [0, 0, 0, 1.0]],
        ];
    }

    public function parseToGrayProvider()
    {
        return [
            [[127, 127, 127], [127, 1.0,]],
            [[127, 127, 127, 0.5], [127, 0.5]],
            ['#7f7f7f', [127, 1.0]],
            ['cmyk(0, 0, 0, 50)', [128, 1.0]],
        ];
    }

    public function parseTo4cProvider()
    {
        return [
            [[0, 0, 0], [0, 0, 0, 100.0,]],
            [[0, 255, 0], [100.0, 0, 100.0, 0,]],
            [[0, 0, 255], [100.0, 100.0, 0, 0,]],
            [[255, 0, 255], [0, 100.0, 0, 0,]],
            [[127, 127, 127], [0, 0, 0, 50.2,]],
            [[0, 0, 255, 0.5], [50.0, 50.0, 0, 0,]],
            [[0, 0, 0, 0.5], [0, 0, 0, 50.0,]],
        ];
    }
}
