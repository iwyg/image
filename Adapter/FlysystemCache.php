<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Adapter;

use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Cache\AbstractCache;
use \Thapp\Image\Resource\FlysystemCachedResource;
use \League\Flysystem\FilesystemInterface;

/**
 * @class FlysystemCache implements CacheInterface
 * @see CacheInterface
 *
 * @package Thapp\JitImage\Adapter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FlysystemCache extends AbstractCache
{
    /**
     * fs
     *
     * @var mixed
     */
    protected $fs;

    /**
     * path
     *
     * @var mixed
     */
    protected $path;

    /**
     * path
     *
     * @var mixed
     */
    protected $metaPath;

    /**
     * Create a new Cache instance
     *
     * @param FilesystemInterface $fs
     * @param string $path
     * @param string $metaPath
     * @param string $prefix
     *
     * @access public
     * @return mixed
     */
    public function __construct(FilesystemInterface $fs, $path = 'cache', $metaPath = null, $prefix = 'fly_')
    {
        $this->fs     = $fs;
        $this->path   = $path;
        $this->prefix = $prefix;
        $this->pool   = [];

        $this->setMetaPath($metaPath);
    }

    /**
     * has
     *
     * @param mixed $id
     *
     * @return boolean
     */
    public function has($id)
    {
        if ($this->poolHas($id)) {

            return true;
        }

        return file_exists($file = $this->getMetaFile($id)) && $this->checkIntegrity($file);
    }

    /**
     * get
     *
     * @param string  $id
     * @param boolean $raw
     *
     * @return \Thapp\Image\Resource\ResourceInterface
     */
    public function get($key, $raw = self::CONTENT_RESOURCE)
    {
        if (!$this->has($key)) {
            return;
        }

        $resource = isset($this->pool[$key]) ? $this->pool[$key] : $this->getResource($key);

        return $raw ? $resource->getContents($key) : $resource;
    }

    /**
     * set
     *
     * @param string $id
     * @param string $contents
     *
     * @return void
     */
    public function set($key, ProcessorInterface $proc)
    {
        $this->pool[$key] = $this->setResource($proc, $key);
    }

    /**
     * Purge the whole cache
     *
     * @return void
     */
    public function purge()
    {
        foreach ($this->fs->listContents($this->path, true) as $item) {

            $path = $item['path'];

            if ('dir' === $item['type']) {
                $this->fs->deleteDir($path);
            }

            if ('file' === $item['type']) {
                $this->fs->delete($path);
            }
        }

        $this->recursiveDelete($this->metaPath);
    }

    /**
     * Purge cache for a file
     *
     * @return void
     */
    public function delete($file)
    {
        $key = $this->createKey($file);

        $this->fs->deleteDir(dirname($this->getPath($key)));

        $this->recursiveDelete($meta = dirname($this->getMetaFile($key)));

        rmdir($meta);
    }

    /**
     * recursiveDelete
     *
     * @param string $dir
     *
     * @return void
     */
    protected function recursiveDelete($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        foreach (new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS) as $path => $item) {
            if ($item->isFile()) {
                unlink($item);
            }

            if ($item->isDir()) {

                $this->recursiveDelete($path);

                rmdir($path);
            }
        }
    }

    /**
     * Create the file resource
     *
     * @param ProcessorInterface $proc
     * @param string $file the path to the source file.
     *
     * @return void
     */
    protected function createResource(ProcessorInterface $proc, $file)
    {
        return new FlysystemCachedResource($proc, $file);
    }

    /**
     * Sets the path for storing meta information
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException if path is not local
     * @return void
     */
    protected function setMetaPath($path)
    {
        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('meta path must be local, "%s" given', $path));
        }

        $this->metaPath = $path;
    }

    /**
     * getPath
     *
     * @param mixed $id
     *
     * @return mixed
     */
    protected function getPath($id)
    {
        list ($base, $file) = $this->parseKey($id);

        $path = $this->path . '/' . $base . '/' . $file;

        return ltrim($path, '/');
    }

    /**
     * set
     *
     * @param string $id
     * @param string $contents
     *
     * @return ResourceInterface
     */
    private function setResource(ProcessorInterface $proc, $key)
    {
        $resource = $this->createResource($proc, $path = $this->getPath($key).'.'.$proc->getFileFormat());

        $this->fs->put($path, $resource->getContents());

        $this->ensureDir($meta = $this->getMetaFile($key));

        file_put_contents($meta, serialize($resource));

        return $resource;
    }

    /**
     * getResource
     *
     * @param mixed $key
     *
     * @return ResourceInterface
     */
    private function getResource($key)
    {
        $resource = unserialize(file_get_contents($this->getMetaFile($key)));
        $resource->setFs($this->fs);

        $this->pool[$key] = $resource;

        return $resource;
    }

    /**
     * checks the existence of a cached resource
     *
     * @param string $meta the meta file path
     *
     * @return boolean
     */
    private function checkIntegrity($meta)
    {
        $resource = unserialize(file_get_contents($meta));

        return $this->fs->has($resource->getPath());
    }

    /**
     * Returns the path to the meta file.
     *
     * @param string $key the cache id
     *
     * @return string
     */
    private function getMetaFile($key)
    {
        list ($prefix, $id) = explode('.', $key);

        return $this->metaPath.DIRECTORY_SEPARATOR.$prefix.DIRECTORY_SEPARATOR.$id;
    }

    /**
     * Make shure the directory of a file exists
     *
     * @param string $file
     *
     * @return void
     */
    private function ensureDir($file)
    {
        if (!is_dir($dir = dirname($file))) {
            @mkdir($dir, 0755, true);
        }
    }
}
