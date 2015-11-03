<?php

/*
 * This File is part of the Thapp\Image\Tests\Info package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Info;

/**
 * @class ReaderTest
 *
 * @package Thapp\Image\Tests\Info
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ReaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider supportedFilesProvider
     */
    public function ensureHasMimeTypeOnSupportedFiles($file, $mime)
    {
        $reader = $this->newReader();
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

    abstract protected function newReader();
}
