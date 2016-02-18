<?php

/*
 * This File is part of the Thapp\Image\Tests\Palette package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Color\Palette;

use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\PaletteInterface;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RgbTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnColor()
    {
        $palette = new Rgb;
        $this->assertInstanceof('Thapp\Image\Color\ColorInterface', $palette->getColor([255, 255, 255]));
    }

    /** @test */
    public function itShouldCacheColors()
    {
        $palette = new Rgb;

        $colorA = $palette->getColor('#ff0000');
        $colorB = $palette->getColor([255, 0, 0]);

        $this->assertSame($colorA, $colorB);
    }

    /** @test */
    public function itShouldGetColorDefinitions()
    {
        $palette = new Rgb;
        $this->assertSame(
            [
                ColorInterface::CHANNEL_RED,
                ColorInterface::CHANNEL_GREEN,
                ColorInterface::CHANNEL_BLUE,
                ColorInterface::CHANNEL_ALPHA
            ],
            $palette->getDefinition()
        );
    }

    /** @test */
    public function itShouldGetItsProfile()
    {
        $this->assertInstanceof(
            'Thapp\Image\Color\Profile\ProfileInterface',
            (new Rgb)->getProfile()
        );
    }

    /** @test */
    public function profileShouldBeSettable()
    {
        $rgb = new Rgb;
        $rgb->setProfile($profile = $this->getMock('Thapp\Image\Color\Profile\ProfileInterface'));
        $this->assertSame($profile, $rgb->getProfile());
    }

    /** @test */
    public function itShouldGetPaletteConstant()
    {
        $rgb = new Rgb;
        $this->assertSame(PaletteInterface::PALETTE_RGB, $rgb->getConstant());
    }
}
