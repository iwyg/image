<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Info;

use Thapp\Image\Info\ExifReader;

/**
 * @class ExifReaderTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ExifReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $reader = new ExifReader;
        /*var_dump($reader->readFromFile('/Users/malcolm/Pictures/IMG_4432.JPG'));*/
    }

    /**
     * @test
     * @dataProvider supportedFilesProvider
     */
    public function ensureHasMimeTypeOnSupportedFiles($file, $mime)
    {
        $reader = new ExifReader;
        $meta = $reader->readFromFile($this->fixure($file));

        $this->assertArrayHasKey('MimeType', $meta);
        $this->assertSame($mime, $meta['MimeType']);

        $meta = $reader->readFromBlob(file_get_contents($this->fixure($file)));

        $this->assertArrayHasKey('MimeType', $meta);
        $this->assertSame($mime, $meta['MimeType']);

        $meta = $reader->readFromStream($handle = fopen($this->fixure($file), 'r+'));
        fclose($handle);
        $this->assertArrayHasKey('MimeType', $meta);
        $this->assertSame($mime, $meta['MimeType']);
    }

    public function supportedFilesProvider()
    {
        return [
            ['pattern.jpg', 'image/jpeg'],
            ['pattern.tiff', 'image/tiff'],
            ['pattern.tif', 'image/tiff'],
            ['white4c.tif', 'image/tiff'],
            ['white4c_lzw.tif', 'image/tiff'],
            ['white4c_zip.tif', 'image/tiff'],
            ['white4c_jpeg.tif', 'image/tiff'],
        ];
    }

    protected function fixure($file)
    {
        return dirname(__DIR__).'/Fixures/'.$file;
    }
}
