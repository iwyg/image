<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Thapp\Image\Tests\Driver\Im;

use Thapp\Image\Driver\Im\Image;
use Thapp\Image\Driver\Im\Source;
use Thapp\Image\Tests\Driver\ImageTest as AbstractImageTest;
use Thapp\Image\Info\ImageReader as FileReader;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Driver\Im\Command\File;
use Thapp\Image\Driver\Im\Command\Format;
use Thapp\Image\Color\Palette\Cmyk;

/**
 * @class ImageTest
 * @see AbstractImageTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageTest extends AbstractImageTest
{
    /** @test */
    public function coalesceShouldReturnFrames()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @dataProvider formatMimeProvider
     */
    public function itShouldSaveToFormat($format, $mime)
    {
        $this->markTestIncomplete();
    }

    ///**
    // * @test
    // * @dataProvider formatMimeProvider
    // */
    //public function itShouldWriteToStream($format, $mime)
    //{
    //    $this->markTestIncomplete();
    //}

    ///** @test */
    //public function itShouldGetColorAtPixel()
    //{
    //    $this->markTestIncomplete();
    //}

    ///** @test */
    //public function itShouldCreateNewImage()
    //{
    //    $this->markTestIncomplete();
    //}

    ///** @test */
    //public function gettingColorOfInvalidPointShouldThrowExpcetion()
    //{
    //    $this->markTestIncomplete();
    //}

    /** @test */
    public function itShouldCopyInstance()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function itShouldGetExifOrientation()
    {
        $this->markTestIncomplete();
    }

    protected function newImage($w, $h, $format = 'jpeg', $reader = null)
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source($reader ?: new FileReader);

        $image = $source->read($resource);

        return $image;
    }

    protected function getDriverName()
    {
        return 'im';
    }

    protected function loadImage($file, $reader = null)
    {
        $image = (new Source($reader === null ? new FileReader : null))->load($file);

        return $image;
    }

    protected function setUp()
    {
        $this->skipIfImagemagick();
        parent::setUp();
    }
}
