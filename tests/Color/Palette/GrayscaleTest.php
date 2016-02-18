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

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\Grayscale;
use Thapp\Image\Color\Palette\PaletteInterface;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class GrayscaleTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnColor()
    {
        $palette = new Grayscale;
        $this->assertInstanceof('Thapp\Image\Color\GrayscaleInterface', $palette->getColor([55, 0.5]));
    }

    /** @test */
    public function itShouldGetColorDefinitions()
    {
        $palette = new Grayscale;
        $this->assertSame([ColorInterface::CHANNEL_GRAY, ColorInterface::CHANNEL_ALPHA], $palette->getDefinition());
    }

    /** @test */
    public function itShouldGetItsProfile()
    {
        $this->assertInstanceof(
            'Thapp\Image\Color\Profile\ProfileInterface',
            (new Grayscale)->getProfile()
        );
    }

    /** @test */
    public function profileShouldBeSettable()
    {
        $gray = new Grayscale;
        $gray->setProfile($profile = $this->getMock('Thapp\Image\Color\Profile\ProfileInterface'));
        $this->assertSame($profile, $gray->getProfile());
    }

    /** @test */
    public function itShouldGetPaletteConstant()
    {
        $gray = new Grayscale;
        $this->assertSame(PaletteInterface::PALETTE_GRAYSCALE, $gray->getConstant());
    }
}
