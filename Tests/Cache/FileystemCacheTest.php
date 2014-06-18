<?php

/**
 * This File is part of the Thapp\Image\Tests\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Cache;


use \Mockery as m;
use \org\bovigo\vfs\vfsStream;
use \Thapp\Image\Cache\CacheInterface;
use \Thapp\Image\Cache\FilesystemCache;
use \Symfony\Component\Filesystem\Filesystem;

/**
 * @class FileystemCacheTest
 * @package Thapp\Image\Tests\Cache
 * @version $Id$
 */
class FileystemCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $fs;

    protected $cache;

    protected $rootPath;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Cache\FilesystemCache', new FilesystemCache);
    }

    protected function getProcMock()
    {
        $proc = m::mock('Thapp\Image\ProcessorInterface');
        $proc->shouldReceive('getTargetSize')->andReturn(['w' => 100, 'h' => 100]);
        $proc->shouldReceive('getContents')->andReturn('');
        $proc->shouldReceive('getMimeType')->andReturn('image/jpeg');
        $proc->shouldReceive('getLastModTime')->andReturn(time());
        $proc->shouldReceive('getFileFormat')->andReturn('jpg');

        return $proc;
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $key = 'foo.bar';

        $this->cache->set($key, $this->getProcMock());
        $this->assertTrue(file_exists($this->rootPath . '/foo/bar.jpg'));
    }

    /** @test */
    public function itIsAbleToRetreiveFromKey()
    {
        $key = 'foo.bar';

        $this->assertFalse($this->cache->has($key));
        $this->cache->set($key, $this->getProcMock());
        $this->assertTrue($this->cache->has($key));

        $this->assertInstanceof('\Thapp\Image\Resource\ResourceInterface', $this->cache->get($key));

        $cache = $this->newSystem();
        $this->assertTrue($cache->has($key));
    }

    /** @test */
    public function itShouldSetResourceAttributes()
    {
        $key = 'foo.bar';

        $this->cache->set($key, $c = $this->getProcMock());
        $res = $this->cache->get($key, CacheInterface::CONTENT_RESOURCE);

        $this->assertInstanceof('\Thapp\Image\Resource\CachedResource', $res);
        //$this->assertFalse($res->isLocal());

        $this->cache->set($key, $c = $this->getProcMock());
        $res = $this->cache->get($key, CacheInterface::CONTENT_STRING);

        $this->assertSame('', $res);
    }

    /** @test */
    public function itShouldBeAbleToCreateKeys()
    {
        $key = $this->cache->createKey('image.jpg', 'string/image.jpg', 'jpg');

        $this->assertSame(31, strlen($key));
        $this->assertTrue(1 === substr_count($key, '.'));
    }

    /** @test */
    public function itShouldPurgeCached()
    {
        $keyA = $this->cache->createKey('image.jpg', 'string/image.jpg', 'jpg');
        $keyB = $this->cache->createKey('image.png', 'string/image.png', 'png');

        $this->cache->set($keyA, $this->getProcMock());
        $this->cache->set($keyB, $this->getProcMock());

        $ra = $this->cache->get($keyA);
        $rb = $this->cache->get($keyB);

        $this->cache->purge();

        $this->assertFalse(file_exists($ra->getPath()));
        $this->assertFalse(file_exists($rb->getPath()));
    }

    /** @test */
    public function itShouldDeleteSelectivly()
    {
        $keyA = $this->cache->createKey('image.jpg', 'string/image.jpg', 'jpg');
        $keyB = $this->cache->createKey('image.png', 'string/image.png', 'png');

        $this->cache->set($keyA, $this->getProcMock());
        $this->cache->set($keyB, $this->getProcMock());

        $ra = $this->cache->get($keyA);
        $rb = $this->cache->get($keyB);

        $this->cache->delete('image.jpg');

        $this->assertFalse(file_exists($ra->getPath()));
        $this->assertTrue(file_exists($rb->getPath()));
    }

    /** @test */
    public function deletingNonFilesShouldReturnFalse()
    {
        $key = $this->cache->createKey('image.jpg', 'string/image.jpg', 'jpg');

        $this->assertFalse($this->cache->delete('image.jpg'));

        $this->cache->set($key, $this->getProcMock());

        $this->assertTrue($this->cache->delete('image.jpg'));
    }

    /** @test */
    public function purgeShouldReturnFalseIfPathIsInvalid()
    {
        $cache = new FilesystemCache(new Filesystem, $this->rootPath . '/cache');

        $this->assertFalse($cache->purge());
    }

    protected function getFsMock()
    {
        return m::mock('Symfony\Component\Filesystem\Filesystem');
    }

    protected function setUp()
    {
        $this->fs = new Filesystem;

        $root = vfsStream::setup('root');

        $this->rootPath  = sys_get_temp_dir() . '/' . time() . 'cache';

        $this->cache = $this->newSystem();
    }

    protected function newSystem()
    {
        return new FilesystemCache(new Filesystem, $this->rootPath);
    }

    protected function tearDown()
    {
        m::close();

        if (is_dir($this->rootPath)) {
            $this->fs->remove($this->rootPath);
        }
    }
}
