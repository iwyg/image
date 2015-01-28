<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gmagick;

use Thapp\Image\Exception\ImageException;
use Thapp\Image\Tests\Driver\SourceTest as Source;

/**
 * @class SourceTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SourceTest extends Source
{
    /** @test */
    public function itShouldThrowExceptionForUnsupportedFormats()
    {
        $source = $this->newSource();
        try {
            $source->load($this->asset('lab.tif'));
        } catch (ImageException $e) {
            $this->assertEquals('Unsupported color space.', $e->getMessage());
            return;
        }

        $this->fail('Sorry');
    }

    public function paletteTestProvider()
    {
        // Gmagick doesn't reckognize grayscale color spaces.
        return [
            //['grayscale.jpg', 'Thapp\Image\Color\Palette\GrayscalePaletteInterface'],
            ['pattern.png', 'Thapp\Image\Color\Palette\RgbPaletteInterface'],
            ['pattern4c.jpg', 'Thapp\Image\Color\Palette\CmykPaletteInterface'],
        ];
    }

    protected function getSourceClass()
    {
        return 'Thapp\Image\Driver\Gmagick\Source';
    }

    protected function setUp()
    {
        if (!class_exists('Gmagick') || (isset($_ENV['IMAGE_DRIVER']) && 'gmagick' !== $_ENV['IMAGE_DRIVER'])) {
            $this->markTestIncomplete();
        }

        foreach ($this->images as $image) {
            $image->destroy();
        }

        $this->images = [];
    }
}
