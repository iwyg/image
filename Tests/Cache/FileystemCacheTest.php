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

    /** @test */
    public function itIsExpectedThat()
    {
        $key = 'foo.bar';

        $this->cache->set($key, 'somecontent');
        $this->assertTrue(file_exists($this->rootPath . '/foo/bar'));
    }

    /** @test */
    public function itIsAbleToRetreiveFromKey()
    {
        $key = 'foo.bar';

        $this->assertFalse($this->cache->has($key));
        $this->cache->set($key, 'somecontent');
        $this->assertTrue($this->cache->has($key));

        $this->assertInstanceof('\Thapp\Image\Resource\ResourceInterface', $this->cache->get($key));

        $cache = $this->newSystem();
        $this->assertTrue($cache->has($key));
    }

    /** @test */
    public function itShouldSetResourceAttributes()
    {
        $key = 'foo.bar';

        $this->cache->set($key, $c = 'somecontent');
        $res = $this->cache->get($key);

        $this->assertTrue($res->isLocal());
        $this->assertSame($c, $res->getContents());
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

        $this->cache->set($keyA, 'somecontent');
        $this->cache->set($keyB, 'somecontent');

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

        $this->cache->set($keyA, 'somecontent');
        $this->cache->set($keyB, 'somecontent');

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

        $this->cache->set($key, 'somecontent');

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
