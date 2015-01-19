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
    public function itShouldBeStringable()
    {
        $color = new Rgb(255, 255, 0);

        $this->assertSame('rgb(255, 255, 0)', (string)$color);

        $color = new Rgb(255, 255, 0, 0.5);

        $this->assertSame('rgba(255, 255, 0, 0.5)', (string)$color);
    }

    protected function newColor($r, $g, $b, $a = null)
    {
        return new Rgb($r, $g, $b, $a);
    }
}
