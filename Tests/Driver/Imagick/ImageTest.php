<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Imagick;

use Imagick;
use Thapp\Image\Driver\Imagick\Image;
use Thapp\Image\Driver\Imagick\Source;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Tests\Driver\ImageTest as AbstractImageTest;
use Thapp\Image\Info\ImageReader as FileReader;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageTest extends AbstractImageTest
{
    protected $handle;

    /** @test */
    public function itShouldSaveImageInterlaced()
    {
        $stream = tmpfile();
        $image = $this->newImage(100, 100);

        $image->write($stream, 'jpeg', ['interlace' => 1]);
        fclose($stream);
    }

    /** @test */
    public function itShouldApplyPngCompression()
    {
        $stream = tmpfile();
        $image = $this->newImage(100, 100);

        $image->write($stream, 'png', ['compression_quality_png' => 99]);
        $this->assertSame(99, $image->getImagick()->getImageCompressionQuality());
        fclose($stream);
    }

    /** @test */
    public function itShouldApplyGifCompression()
    {
        $stream = tmpfile();
        $image = $this->newImage(100, 100);

        $image->write($stream, 'gif', ['compression_quality_gif' => 20]);
        $this->assertSame(20, $image->getImagick()->getImageCompressionQuality());
        fclose($stream);
    }

    /** @test */
    public function itShouldApplyTiffCompression()
    {
        $stream = tmpfile();
        $image = $this->newImage(100, 100);

        $image->write($stream, 'tiff', ['compression_quality_tiff' => 20]);
        $this->assertSame(20, $image->getImagick()->getImageCompressionQuality());

        fclose($stream);
    }

    /** @test */
    public function itShouldGetImagick()
    {
        $image = $this->newImage(100, 100);
        $this->assertInstanceof('Imagick', $image->getImagick());
    }

    /** @test */
    public function imagickShouldBeSwapabled()
    {
        $image = $this->newImage(100, 100);
        $image->swapImagick($imagick = new \Imagick);

        $this->assertSame($imagick, $image->getImagick());
    }

    /** @test */
    public function itShouldDetectFrames()
    {
        $image = $this->newImage(100, 100);
        $this->assertFalse($image->hasFrames());
    }

    /**
     * @test
     * @dataProvider rgbToCmykProvider
     */
    public function itShouldConvertRgbToCmykColorspace($source, $format)
    {
        $src = new Source;
        $image = $this->loadImage($this->asset($source));
        $image->applyPalette(new Cmyk);

        $dst = $src->create($image->getBlob($format));
        $image->destroy();

        $this->assertInstanceof('Thapp\Image\Color\Palette\CmykPaletteInterface', $dst->getPalette());
    }

    public function rgbToCmykProvider()
    {
        return [
            ['pattern.tiff', 'TIFF'],
            ['pattern.tiff', 'JPEG'],
            ['pattern.png', 'TIFF'],
            ['pattern.png', 'JPEG']
        ];
    }

    /** @test */
    public function itShouldSaveImageWithWhiteBackground()
    {
        $image = $this->loadImage($this->asset('transparent4.png'));
        $image->setGravity(new Gravity(5));
        $image->edit()->extent(new Size(800, 800));
        //$image->edit()->extent(new Size(1000, 1000), null, $image->getPalette()->getColor('#ff0000'));
        $image->setFormat('PNG');
        $image->save($this->asset('_save_as_png.png'));
        //$content = $image->getBlob();
        //$image->destroy();

        //$image = (new Source)->create($content);

        //$color = $image->getColorAt(new Point(1, 1));
        //var_dump($color);
    }


    protected function loadImage($file, $reader = null)
    {
        $image = (new Source($reader === null ? new FileReader : null))->load($file);

        return $image;
    }

    protected function getDriverName()
    {
        return 'imagick';
    }

    protected function newImage($w, $h, $format = 'jpeg', $reader = null)
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source($reader ?: new FileReader);

        return $source->read($resource);
    }

    protected function setUp()
    {
        $this->skipIfImagick();

        parent::setUp();
    }

    protected function tearDown()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}
