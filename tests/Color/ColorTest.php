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

/**
 * @class ColorTest
 *
 * @package Thapp\Image\Tests\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ColorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function rgbConversionShouldReturnNewObject()
    {
        //$color = $this->newColor(255, 255, 255);
        //$nc = $color->toRgb();

        //$this->assertInstanceof('Thapp\Image\Color\Rgb', $nc);

        //$this->assertFalse($nc === $color);
        //$this->assertSame('rgb(255,255,255)',  (string)$nc);
    }

    /** @test */
    public function hexConversionShouldReturnNewObject()
    {
        //$color = $this->newColor(255, 255, 255);
        //$nc = $color->toHex();

        //$this->assertInstanceof('Thapp\Image\Color\Hex', $nc);

        //$this->assertFalse($nc === $color);
        //$this->assertSame('#ffffff',  (string)$nc);
    }

    abstract protected function newColor($r, $g, $b, $a = null);
}
