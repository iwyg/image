<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Color\Rgb;
use Thapp\Image\Color\Palette\Rgb as RgbPalette;
use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ImageTest extends \PHPUnit_Framework_TestCase
{
    use ImageTestHelper,
        TestHelperTrait;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->newImage(100, 100));
    }

    /** @test */
    public function itShouldGetImageFormat()
    {
        $image = $this->newImage(200, 100);

        $this->assertSame('jpeg', $image->getFormat());

        $image = $this->newImage(200, 100, 'png');

        $this->assertSame('png', $image->getFormat());
        $image->setFormat('JPG');
        $this->assertSame('jpeg', $image->getFormat());
    }

    /** @test */
    public function itShouldReturnEditInstance()
    {
        $image = $this->newImage(200, 100);
        $this->assertInstanceof('Thapp\Image\Driver\EditInterface', $image->edit());
    }

    /** @test */
    public function coalesceShouldReturnFrames()
    {
        $image = $this->newImage(200, 100);
        $this->assertInstanceof('Thapp\Image\Driver\FramesInterface', $image->coalesce());
    }

    /** @test */
    public function itShouldGetOrientation()
    {
        $image = $this->newImage(200, 100);
        $this->assertSame(ImageInterface::ORIENT_UNDEFINED, $image->getOrientation());
    }

    /** @test */
    public function gettingColorOfInvalidPointShouldThrowExpcetion()
    {
        $image = $this->newImage(1, 1);
        try {
            $image->getColorAt(new Point(2, 2));
        } catch (\OutOfBoundsException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldCreateNewImage()
    {
        $image = $this->newImage(200, 100);
        $new = $image->newImage();

        $this->assertInstanceof(get_class($image), $new);
        $this->assertFalse($image === $new);

        $this->assertSame($image->getFormat(), $new->getFormat());
        $this->assertSame($image->getWidth(), $new->getWidth());
        $this->assertSame($image->getHeight(), $new->getHeight());
    }

    /**
     * @test
     * @dataProvider formatMimeProvider
     */
    public function itShouldSaveToFormat($format, $mime)
    {
        $image = $this->newImage(100, 100);
        $path = tempnam(sys_get_temp_dir(), 'image');

        $image->setFormat($format);

        try {
            $image->save($path, $format);
        } catch (\Exception $e) {
            unlink($path);
            throw $e;
        }

        $info = getimagesize($path);
        unlink($path);

        $this->assertSame($mime, $info['mime']);
    }

    /**
     * @test
     * @expectedException \Thapp\Image\Exception\ImageException
     */
    public function itShouldThrowOnSaveIfPathIsInvalid()
    {
        $image = $this->newImage(100, 100);
        $image->save('/idontexists');
    }

    /**
     * @test
     * @expectedException \Thapp\Image\Exception\ImageException
     */
    public function itShouldThrowOnWriteIfStreamIsInvalid()
    {
        $image = $this->newImage(100, 100);
        $image->write(null);
    }

    /**
     * @test
     * @expectedException \Thapp\Image\Exception\ImageException
     */
    public function itShouldThrowOnWriteIfStreamIsReadOnly()
    {
        $path = tempnam(sys_get_temp_dir(), 'image');
        $stream = fopen($path, 'rb');
        $image = $this->newImage(100, 100);
        $error = null;
        try {
            $image->write($stream);
        } catch (\Exception $e) {
            $error = $e;
        }

        fclose($stream);
        unlink($path);

        if (null !== $error) {
            throw $e;
        }
    }

    /**
     * @test
     * @dataProvider formatMimeProvider
     */
    public function itShouldWriteToStream($format, $mime)
    {
        $stream = tmpfile();
        $image = $this->newImage(100, 100);
        $meta = stream_get_meta_data($stream);

        try {
            $image->write($stream, $format);
        } catch (\Exception $e) {
            fclose($stream);
            throw $e;
        }

        rewind($stream);
        $info = getimagesizefromstring(stream_get_contents($stream));

        fclose($stream);

        $this->assertSame($mime, $info['mime']);
    }

    public function formatMimeProvider()
    {
        return [
            ['jpg', 'image/jpeg'],
            ['jpeg', 'image/jpeg'],
            ['gif', 'image/gif'],
            ['png', 'image/png'],
            ['wbmp', 'image/vnd.wap.wbmp'],
            ['xbm', 'image/xbm'],
        ];
    }

    /** @test */
    public function itShouldCropWithGravity()
    {
        //$image->save($this->asset('pattern_copy_'.$this->getDriverName().'.png'));

        //if (false === $image = $this->loadImage($file = $this->asset('google.png'))) {
        //    $this->fail(sprintf('Loading of "%s" image file %s failed.', $this->getDriverName(), $file));
        //}

        //var_dump($image->getPalette());

        //var_dump($color = $image->getColorAt(new Point(240, 150)));
        //var_dump($color->getColor());
        //var_dump($color->getColorAsString());
        //$image->destroy();
        //$image->setGravity(new Gravity(5));
        //foreach ($image->coalesce() as $frame) {
            //$frame->edit()->crop(new Size(100, 100));
        //}

        //$image->save($this->asset('animated_'.$this->getDriverName().'.gif'));
    }

    /** @test */
    public function itShouldCopyInstance()
    {
        $image = $this->newImage(200, 200);
        $copy  = $this->images[] = $image->copy();

        $this->assertFalse($image === $copy, 'Image should not equal copy.');
        $this->assertFalse($image->frames() === $copy->frames(), 'frames should not equal copied frames.');
        $this->assertFalse($image->getMetaData() === $copy->getMetaData(), 'metadata should not equal copied metadata.');
        $this->assertSame($image->getPalette(), $copy->getPalette(), 'palette should equal copied palette.');
        $copy->destroy();
    }

    /** @test */
    public function itShouldGetColorAtPixel()
    {
        $image = $this->loadImage($file = $this->asset('transparent4.png'));

        $colorA = $image->getColorAt(new Point(0, 0));
        $colorB = $image->getColorAt(new Point(150, 0));

        $this->assertInstanceof('Thapp\Image\Color\RgbInterface', $colorA);
        $this->assertTrue(1 > $colorA->getAlpha());
    }

    abstract protected function newImage($w, $h, $format = 'jpeg');
    abstract protected function getDriverName();
    abstract protected function loadImage($file);

    protected function getImagePath($resource)
    {
        $meta = stream_get_meta_data($resource);

        return $meta['uri'];
    }

    protected function setUp()
    {
        $this->assets = __DIR__.'/Fixures';
    }
}
