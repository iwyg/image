<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Traits;

use \org\bovigo\vfs\vfsStream;
use \Thapp\Image\Traits\FileHelper;

/**
 * @class FileHelperTest
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FileHelperTest extends \PHPUnit_Framework_TestCase
{
    use FileHelper;

    private $root;

    private $rootPath;

    protected function setUp()
    {
        $this->root     = vfsStream::setup('root');
        $this->rootPath = vfsStream::url('root');
    }

    /** @test */
    public function itShouldDumpFileContents()
    {
        $this->dumpFile($file = $this->rootPath . '/target/file', $content = 'some content');

        $this->assertFileExists($file);
        $this->assertSame($content, file_get_contents($file));
    }

    /** @test */
    public function itShouldEnsurADirectory()
    {
        $this->ensureDir($dir = $this->rootPath . '/test');

        $this->assertFileExists($dir);

        $this->ensureDir($dir = $this->rootPath . '/foo/bar');

        $this->assertFileExists($dir);
    }

    /** @test */
    public function itShouldSweepDirectories()
    {
        $this->ensureDir($dir = $this->rootPath . '/foo/bar');

        $this->dumpFile($fileA = dirname($dir) . '/somefile', '');
        $this->dumpFile($fileB = $dir . '/somefile', '');
        $this->dumpFile($fileC = $dir . '/anotherfile', '');

        $this->assertFileExists($fileA);
        $this->assertFileExists($fileB);
        $this->assertFileExists($fileC);

        $this->sweepDir($dir);

        $this->assertTrue(file_exists($dir));
        $this->assertFalse(file_exists($fileB));
        $this->assertFalse(file_exists($fileC));
    }

    /** @test */
    public function itShouldDeleteDirectories()
    {
        $this->ensureDir($dir = $this->rootPath . '/foo/bar');

        $this->dumpFile($fileA = dirname($dir) . '/somefile', '');
        $this->dumpFile($fileB = $dir . '/somefile', '');

        $this->assertFileExists($fileA);
        $this->assertFileExists($fileB);

        $this->deleteDir($dir);

        $this->assertFalse(file_exists($dir));
    }

    /** @test */
    public function itShouldReturnBooleanOnRemoveOps()
    {
        $this->ensureDir($dir = $this->rootPath . '/foo/bar');

        $this->assertTrue($this->sweepDir($dir));
        $this->assertTrue($this->deleteDir(dirname($dir)));

        $this->assertFalse($this->sweepDir($dir));
        $this->assertFalse($this->deleteDir(dirname($dir)));
    }

    /** @test */
    public function itShouldCheckExistance()
    {
        $this->assertTrue($this->isDir(__DIR__));
        $this->assertFalse($this->isDir(__FILE__));
        $this->assertFalse($this->isFile(__DIR__));
        $this->assertTrue($this->isFile(__FILE__));

        $this->assertTrue($this->exists(__DIR__));
        $this->assertTrue($this->exists(__FILE__));

        $this->assertFalse($this->exists('foo'));
        $this->assertFalse($this->exists('bar'));
    }
}
