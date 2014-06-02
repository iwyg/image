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
     * @var mixed
     */
    protected $path;

    /**
     * pool
     *
     * @var mixed
     */
    protected $pool;

    /**
     * @param string $location
     */
    public function __construct($location)
    {
        $this->path = $location;

        $this->pool = [];
    }

    /**
     * getCacheKey
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function getCacheKey($param)
    {
        return null;
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @return string
     */
    public function get($id)
    {
        return null;
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
        file_put_contents($this->realizeDir($id), $content);
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
     * getSource
     *
     * @param mixed $key
     *
     * @access public
     * @return string
     */
    public function getSource($key)
    {
        return $this->pool[$key];
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

        //array_shift($parsed);

        list ($dir, $file) = $parsed;

        return sprintf('%s%s%s%s%s', $this->path, DIRECTORY_SEPARATOR, $dir, DIRECTORY_SEPARATOR, $file);
    }
}
