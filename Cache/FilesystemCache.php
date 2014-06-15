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

use \FilesystemIterator;
use \Thapp\Image\ImageInterface;
use \Thapp\Image\Resource\CachedResource;
use \Symfony\Component\Filesystem\Filesystem;

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

    /**
     * @param string $location
     */
    public function __construct(Filesystem $fs = null, $location = null, $prefix = 'fs_')
    {
        $this->fs     = $fs ?: new Filesystem;
        $this->path   = $location ?: getcwd();
        $this->prefix = $prefix;

        $this->pool = [];
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @return string
     */
    public function get($id, $raw = false)
    {
        return $raw ? file_get_contents($this->getSource($id)) : $this->createSource($id);
    }

    /**
     * set
     *
     * @param string $id
     * @param Image $image
     *
     * @return void
     */
    public function set($id, $content)
    {
        $this->fs->dumpFile($file = $this->realizeDir($id), $content);

        $this->pool[$id] = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->pool)) {
            return true;
        }

        if (file_exists($path = $this->getPath($key))) {
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
        if (!$this->fs->exists($this->path)) {
            return false;
        }

        try {
            foreach (new FilesystemIterator($this->path, FilesystemIterator::SKIP_DOTS) as $file) {
                $this->fs->remove($file);
            }
        } catch (\Exception $e) {
        }

        return true;
    }

    public function delete($file)
    {
        $key = $this->createKey($file);
        $dir = substr($key, 0, strpos($key, '.'));

        if ($this->fs->exists($dir = $this->path . '/' . $dir)) {
            $this->fs->remove($dir);

            return true;
        }

        return false;
    }

    /**
     * getSource
     *
     * @param mixed $key
     *
     * @access public
     * @return string
     */
    public function getSource($key)
    {
        return isset($this->pool[$key]) ? $this->pool[$key] : $this->getPath($key);
    }

    /**
     * realizeDir
     *
     * @param mixed $key
     *
     * @access protected
     * @return string
     */
    protected function realizeDir($key)
    {
        $path = $this->getPath($key);

        if (!is_dir($dir = dirname($path))) {
            @mkdir($dir, 0775, true);
        }

        return $path;
    }

    /**
     * Get the cachedirectory from a cache key.
     *
     * @param string $key
     *
     * @access protected
     * @return string the dirctory path of the cached item
     */
    protected function getPath($key)
    {
        $parsed = $this->parseKey($key);

        list ($dir, $file) = $parsed;

        return sprintf('%s%s%s%s%s', $this->path, DIRECTORY_SEPARATOR, $dir, DIRECTORY_SEPARATOR, $file);
    }

    /**
     * createSource
     *
     * @param mixed $id
     *
     * @access private
     * @return ResourceInterface
     */
    private function createSource($id)
    {
        $file = $this->getSource($id);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime = finfo_file($finfo, $file);

        finfo_close($finfo);

        return new CachedResource(
            $file,
            function () use ($file) {
                return file_get_contents($file);
            },
            filemtime($file),
            $mime
        );
    }
}
