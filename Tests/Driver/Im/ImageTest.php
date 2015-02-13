<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Im package
 *
 * (c)  <>
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
 *
 * @package
 * @version $Id$
 * @author  <>
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

    /**
     * @test
     * @dataProvider formatMimeProvider
     */
    public function itShouldWriteToStream($format, $mime)
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function itShouldGetColorAtPixel()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function itShouldCreateNewImage()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function gettingColorOfInvalidPointShouldThrowExpcetion()
    {
        $this->markTestIncomplete();
    }

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

    /** @test */
    public function itIsExpectedThat()
    {
        $image = $this->loadImage($file = $this->asset('transparent4.png'));

        var_dump($image->getColorAt(new Point(50, 50)));

        //$image->setGravity(new Gravity(5));
        ////$image->edit()->extent(new Size(200, 200), null, $image->getPalette()->getColor('#ff00ff'));
        //$image->applyPalette(new Cmyk);
        //var_dump($image->convert);

        //$image->convert->run(new File($file, 'JPEG'), new File($this->asset('_im_test.jpg'), 'JPEG'));

        //$new = $image->newImage('TIFF', $color = $image->getPalette()->getColor('#ccccff'));

        //file_put_contents($this->asset('newfile.tif'), $new->getBlob('TIFF'));
    }

    protected function newImage($w, $h, $format = 'jpeg', $reader = null)
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source($reader ?: new FileReader);

        return $source->read($resource);
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
}
