<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Writer;

use \org\bovigo\vfs\vfsStream;
use \Thapp\Image\Writer\FilesystemWriter;

/**
 * @class FilesystemWriterTest
 * @package Thapp\Image
 * @version $Id$
 */
class FilesystemWriterTest extends \PHPUnit_Framework_TestCase
{
    protected $root;
    protected $path;
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Writer\WriterInterface', new FilesystemWriter);
    }

    /** @test */
    public function itShouldDumpContentToTarget()
    {
        $writer = new FilesystemWriter;

        $file = $this->path . '/location/somefile.txt';

        $writer->write($file, 'some text');
        $this->assertTrue(is_file($file));
    }

    protected function setUp()
    {
        $this->root = vfsStream::setup('root');
        $this->path = vfsStream::url('root');
    }
}
