<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter;

use Thapp\Image\Filter\HslHelperTrait;

/**
 * @class HslHelperTest
 *
 * @package Thapp\Image\Tests\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HslHelperTest extends \PHPUnit_Framework_TestCase
{
    use HslHelperTrait;

    /**
     * @test
     * @dataProvider rgbToHslProvider
     */
    public function convertsRgbToHsl($rgb, $hsl)
    {
        list ($r, $g, $b) = $rgb;
        $this->assertSame($hsl, $this->rgbToHsl($r, $g, $b));
    }

    /**
     * @test
     * @dataProvider rgbToHslProvider
     */
    public function convertsHslToRgb($rgb, $hsl)
    {
        list ($h, $s, $l) = $hsl;
        $this->assertSame($rgb, $this->hslToRgb($h, $s, $l));
    }

    public function rgbToHslProvider()
    {
        return [
            [[0, 0, 0], [0, 0, 0]],
            [[255, 255, 255], [0, 0, 1]],
            [[255, 0, 0], [0.0, 1.0, 0.5]],
            [[0, 255, 0], [120.0, 1.0, 0.5]],
            [[0, 0, 255], [240.0, 1.0, 0.5]],
            [[255, 255, 0], [60.0, 1.0, 0.5]],
            [[0, 255, 255], [180.0, 1.0, 0.5]],
            [[255, 0, 255], [300.0, 1.0, 0.5]],
        ];
    }
}
