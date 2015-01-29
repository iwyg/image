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

use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\ColorInterface;

/**
 * @class RgbTest
 *
 * @package Thapp\Image\Tests\Palette
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CmykTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnColor()
    {
        $palette = new Cmyk;
        $this->assertInstanceof('Thapp\Image\Color\CmykInterface', $palette->getColor('ff7fef'));
    }

    /** @test */
    public function itShouldCacheColors()
    {
        $palette = new Cmyk;

        $colorA = $palette->getColor('rgb(0,0,0)');
        $colorB = $palette->getColor('cmyk(0,0,0,100)');

        $this->assertSame($colorA, $colorB);

        $colorC = $palette->getColor('#fff');
        $colorD = $palette->getColor('cmyk(0,0,0,0)');

        $this->assertSame($colorC, $colorD);
    }

    /** @test */
    public function itShouldGetColorDefinitions()
    {
        $palette = new Cmyk;
        $this->assertSame(
            [
                ColorInterface::CHANNEL_CYAN,
                ColorInterface::CHANNEL_MAGENTA,
                ColorInterface::CHANNEL_YELLOW,
                ColorInterface::CHANNEL_KEY
            ],
            $palette->getDefinition()
        );
    }
}
