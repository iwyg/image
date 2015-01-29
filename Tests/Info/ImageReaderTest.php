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

use Thapp\Image\Info\ImageReader;

/**
 * @class ExifReaderTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageReaderTest extends ReaderTest
{

    /** @test */
    public function itShouldFailOnInvalidFiles()
    {
        $reader = $this->newReader();

        try {
            $reader->readFromFile($this->fixure('invalid.png'));
        } catch (\RuntimeException $e) {
            $this->assertEquals('Cannot read file.', $e->getMessage());
        }

        try {
            $reader->readFromBlob('');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Cannot read info from string.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldReadInfoFromNonLocalStreams()
    {
        $url = 'http://upload.wikimedia.org/wikipedia/commons/4/4a/Logo_2013_Google.png';
        $stream = fopen($url, 'r');

        $reader = $this->newReader();
        $reader->readFromStream($stream);

        fclose($stream);
    }

    protected function newReader()
    {
        return new ImageReader;
    }
}
