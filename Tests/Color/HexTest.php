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

use Thapp\Image\Color\Hex;
use Thapp\Image\Color\Parser;

/**
 * @class HexTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HexTest extends ColorTest
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionIfColorstringIsInvalid()
    {
        $color = new Hex('#ibcdef');

    }

    /** @test */
    public function itShouldBeStringable()
    {
        $color = new Hex('fff');

        $this->assertSame('#ffffff', (string)$color);
    }

    protected function newColor($r, $g, $b, $a = null)
    {
        return new Hex(Parser::rgbToHex($r, $g, $b));
    }
}
