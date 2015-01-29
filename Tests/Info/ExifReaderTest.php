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
class ExifReaderTest extends ReaderTest
{
    /** @test */
    public function itShouldReturnEmprySetForUnsupportedImages()
    {
        $reader = new ExifReader;
        $meta = $reader->readFromFile($this->fixure('pattern.png'));

        $this->assertSame([], $meta->all());
    }

    protected function newReader()
    {
        return new ExifReader;
    }
}
