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
}
