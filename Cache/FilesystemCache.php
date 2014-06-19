<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Cache;

use \Thapp\Image\ImageInterface;
use \Thapp\Image\Traits\FileHelper;
use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Resource\CachedResource;

/**
 * @class FilesystemCache implements CacheInterface
 * @see CacheInterface
 *
 * @package Thapp\Image\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FilesystemCache extends AbstractCache
{
    use FileHelper;
    /**
     * path
     *
     * @var string
     */
    protected $path;

    /**
     * pool
     *
     * @var array
     */
    protected $pool;

    protected $resources;

    /**
     * fs
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * prefix
     *
     * @var string
     */
    protected $prefix;

    protected $metaKey;

    /**
     * @param string $location
     */
    public function __construct($location = null, $prefix = 'fs_', $metaKey = 'meta', $metaPath = null)
    {
        $this->path   = $location ?: getcwd();
        $this->prefix = $prefix;

        $this->metaKey  = $metaKey;
        $this->metaPath = $metaPath ?: $this->path;

        $this->pool = [];
        $this->resources = [];
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @return string
     */
    public function get($key, $raw = self::CONTENT_RESOURCE)
    {
        if (!$this->has($key)) {
            return;
        }

        $resource = $this->getMeta($key);

        return $raw ? $resource->getContents() : $resource;
    }

    /**
     * set
     *
     * @param string $id
     * @param Image $image
     *
     * @return void
     */
    public function set($id, ProcessorInterface $proc)
    {
        $this->writeCache($id, $proc);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->pool)) {
            return true;
        }

        if (file_exists($path = $this->getMetaPath($key))) {
            $this->pool[$key] = $path;

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        return $this->sweepDir($this->path);
    }

    /**
     * Delete the base directory of an cached image.
     *
     * @param string $file
     *
     * @return boolean
     */
    public function delete($file)
    {
        $key = $this->createKey($file);
        $dir = substr($key, 0, strpos($key, '.'));

        return $this->deleteDir($this->path . DIRECTORY_SEPARATOR . $dir);
    }

    /**
     * Get the path to a meta file based on a key.
     *
     * @param string $key
     *
     * @return string
     */
    private function getMetaPath($key)
    {
        list ($path, $file) = array_pad(explode('.', $key), 2, null);

        return $this->metaPath.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file.'.'. $this->metaKey;
    }

    /**
     * Read the meta file contents.
     *
     * @param string $file
     *
     * @return ResourceInterface a cachedResource
     */
    private function getMeta($key)
    {
        if (array_key_exists($key, $this->resources)) {
            return $this->resources[$key];
        }

        return $this->resources[$key] = unserialize(file_get_contents($this->getMetaPath($key)));
    }

    /**
     * writes the cache files.
     *
     * @param string $id
     * @param ProcessorInterface $proc
     *
     * @return void
     */
    private function writeCache($id, ProcessorInterface $proc)
    {
        $fname = $this->getPath($id);

        $file = $fname.'.'.$proc->getFileFormat();
        $meta = $this->getMetaPath($id);

        $metaContent = serialize($resource = new CachedResource($proc, $file));

        $this->dumpFile($meta, $metaContent);
        $this->dumpFile($file, $proc->getContents());

        $this->pool[$id] = $file;
        $this->resources[$id] = $resource;
    }

    /**
     * Get the cachedirectory from a cache key.
     *
     * @param string $key
     *
     * @return string the dirctory path of the cached item
     */
    protected function getPath($key)
    {
        $parsed = $this->parseKey($key);

        list ($dir, $file) = $parsed;

        return sprintf('%s%s%s%s%s', $this->path, DIRECTORY_SEPARATOR, $dir, DIRECTORY_SEPARATOR, $file);
    }
}
