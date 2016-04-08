<?php

/*
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use Thapp\Image\Exception\ImageException;
use Thapp\Image\Info\MetaDataReaderInterface;
use Thapp\Image\Tests\TestHelperTrait;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class SourceTest extends \PHPUnit_Framework_TestCase
{
    use ImageTestHelper,
        TestHelperTrait;

    protected $images = [];

    /** @test */
    public function itShouldThrowOnLoadingIvalidString()
    {
        $source = $this->newSource();

        try {
            $source->create('not an image');
        } catch (ImageException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowOnLoadingIvalidSteam()
    {
        $source = $this->newSource();
        $handle = tmpfile();

        try {
            $source->read('not a stream');
        } catch (ImageException $e) {
            $this->assertSame('Supplied resource is invalid.', $e->getMessage());
        }

        try {
            $source->read($handle);
        } catch (ImageException $e) {
            fclose($handle);
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowOnLoadingIvalidFile()
    {
        $source = $this->newSource();

        try {
            $source->load('not a file');
        } catch (ImageException $e) {
            $this->assertSame('Loading image from file failed.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldReadResource()
    {
        $source = $this->newSource();
        $stream = $this->getTestImage();
        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->images[] = $source->read($stream));
    }

    /** @test */
    public function itShouldCallAReadMethodOnTheMetaDataReader()
    {
        $source = $this->newSource($reader = $this->mockReader());
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
        $this->assertInstanceof(
            'Thapp\Image\Driver\ImageInterface',
            $this->images[] = $source->create(stream_get_contents($stream))
        );
    }

    /**
     * @test
     * @dataProvider paletteTestProvider
     */
    public function itShouldSetCorrectPalette($file, $palette)
    {
        $source = $this->newSource();
        $image  = $this->images[] = $source->load($this->asset($file));

        $this->assertInstanceOf($palette, $image->getPalette(), sprintf('Assertion failed for %s.', $file));
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

    protected function mockReader()
    {
        return $this->getMockBuilder('Thapp\Image\Info\MetaDataReaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function setUp()
    {
        $this->assets = dirname(__DIR__).'/Fixures';
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
