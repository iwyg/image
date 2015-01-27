<?php

/*
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests;

use Thapp\Image\Info\MetaDataReaderInterface;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class SourceTest extends \PHPUnit_Framework_TestCase
{
    use ImageTestHelper;

    protected $images = [];

    /** @test */
    //abstract public function itShouldThrowExceptionForUnsupportedFormats();

    /** @test */
    public function itShouldReadResource()
    {
        $source = $this->newSource();
        $stream = $this->getTestImage();
        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->images[] = $source->read($stream));
    }

    /** @test */
    public function itShouldLoadImageFile()
    {
        $source = $this->newSource();
        $stream = $this->getTestImage();

        $meta = stream_get_meta_data($stream);

        if (empty($meta['uri'])) {
            fclose($stream);
            $file = $this->asset('google.png');
        } else {
            $file = $meta['uri'];
        }

        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->images[] = $source->load($file));
    }

    /** @test */
    public function itShouldCreateImageFromBlob()
    {
        $source = $this->newSource();
        $stream = $this->getTestImage();
        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->images[] = $source->create(stream_get_contents($stream)));
    }

    /**
     * @test
     * @dataProvider paletteTestProvider
     */
    public function itShouldSetCorrectPalette($file, $palette)
    {
        $source = $this->newSource();
        $image  = $source->load($this->asset($file));

        $this->assertInstanceOf($palette, $image->getPalette());
    }

    public function paletteTestProvider()
    {
        return [
            ['pattern.png', 'Thapp\Image\Color\Palette\RgbPaletteInterface'],
            ['grayscale.jpg', 'Thapp\Image\Color\Palette\GrayscalePaletteInterface'],
            ['pattern4c.jpg', 'Thapp\Image\Color\Palette\CmykPaletteInterface'],
        ];
    }

    protected function newSource(MetaDataReaderInterface $reader = null)
    {
        $class = $this->getSourceClass();

        return new $class($reader);
    }

    protected function setUp()
    {
        $this->assets = __DIR__.'/Fixures';
    }

    protected function tearDown()
    {
        // prevent segfault oon Gmagick
        foreach ($this->images as $image) {
            $image->destroy();
        }

        $this->images = [];
    }

    abstract protected function getSourceClass();
}
