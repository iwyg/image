<?php

/**
 * This File is part of the Thapp\Image\Tests\Driver\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Loader;

use \org\bovigo\vfs\vfsStream;
use \Thapp\Image\Driver\Loader\FilesystemLoader;

/**
 * @class FilesystemLoaderTest
 * @package Thapp\Image\Tests\Driver\Loader
 * @version $Id$
 */
class FilesystemLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $rootDir;
    protected $fileRoot;

    protected function setUp()
    {
        $this->fileRoot = vfsStream::setup('root');
        $this->rootDir = vfsStream::url('root');
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Driver\Loader\LoaderInterface', new FilesystemLoader);
    }

    /** @test */
    public function itShouldSupportLocalFiles()
    {
        $loader = new FilesystemLoader;
        touch($file = $this->rootDir . DIRECTORY_SEPARATOR . 'image.jpg');

        $this->assertTrue($loader->supports($file));
    }

    /** @test */
    public function itShouldLoadLocalFiles()
    {
        $loader = new FilesystemLoader;
        touch($file = $this->rootDir . DIRECTORY_SEPARATOR . 'image.jpg');
        $this->createDummySource($file);

        $this->assertTrue(file_exists($loader->load($file)));
    }

    /** @test */
    public function itShouldThrowExceptionOnLoadingInvalidFile()
    {
        $loader = new FilesystemLoader;
        $file = $this->rootDir . DIRECTORY_SEPARATOR . 'image.jpg';

        try {
            $loader->load($file);
        } catch (\Thapp\Image\Exception\SourceLoaderException $e) {
            $this->assertEquals('Invalid Source URL: ' . $file, $e->getMessage());
            return;
        }

        $this->fail('test failed');
    }

    /** @test */
    public function itShouldReturnItsSource()
    {
        $loader = new FilesystemLoader;
        touch($file = $this->rootDir . DIRECTORY_SEPARATOR . 'image.jpg');
        $this->createDummySource($file);

        $loader->load($file);

        $this->assertSame($file, $loader->getSource());
    }

    /** @test */
    public function itShouldCleanItSource()
    {
        $loader = new FilesystemLoader;
        touch($file = $this->rootDir . DIRECTORY_SEPARATOR . 'image.jpg');
        $this->createDummySource($file);

        $loader->load($file);

        $loader->clean();

        $this->assertNull($loader->getSource(), 'source should ne null after ->clean()');
    }

    /**
     * createDummySource
     *
     * @param mixed $file
     *
     * @access protected
     * @return void
     */
    protected function createDummySource($file)
    {
        $image = imagecreatetruecolor(1, 1);

        ob_start();
        imagejpeg($image);
        $contents = ob_get_contents();

        ob_end_clean();

        file_put_contents($file, $contents);
    }
}
