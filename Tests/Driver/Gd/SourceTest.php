<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gd;

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
            $source->load($this->asset('unsupported.pbm'));
        } catch (ImageException $e) {
            $this->assertEquals('Loading image from file failed.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    public function paletteTestProvider()
    {
        return [
            ['pattern.png', 'Thapp\Image\Color\Palette\RgbPaletteInterface'],
            ['grayscale.jpg', 'Thapp\Image\Color\Palette\RgbPaletteInterface'],
            ['pattern4c.jpg', 'Thapp\Image\Color\Palette\RgbPaletteInterface'],
        ];
    }

    protected function getSourceClass()
    {
        return 'Thapp\Image\Driver\Gd\Source';
    }

    protected function setUp()
    {
        if (isset($_ENV['IMAGE_DRIVER']) && 'gd' !== $_ENV['IMAGE_DRIVER']) {
            $this->markTestIncomplete();
        }

        parent::setUp();
    }
}
