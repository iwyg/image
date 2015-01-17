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
}
