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
            $file = __DIR__.'/Fixures/google.png';
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

    protected function newSource()
    {
        $class = $this->getSourceClass();

        return new $class;
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
