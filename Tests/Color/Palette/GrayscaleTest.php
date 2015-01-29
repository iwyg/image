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
        $this->assertInstanceof('Thapp\Image\Color\GrayscaleInterface', $palette->getColor([55,0.5]));
    }

    /** @test */
    public function itShouldGetColorDefinitions()
    {
        $palette = new Grayscale;
        $this->assertSame([ColorInterface::CHANNEL_GRAY, ColorInterface::CHANNEL_ALPHA], $palette->getDefinition());
    }
}
