<?php

/**
 * This File is part of the Thapp\Image\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Cache;

/**
 * @class AbstractCache
 * @package Thapp\Image\Cache
 * @version $Id$
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * pool
     *
     * @var mixed
     */
    protected $pool;

    protected $prefix;

    /**
     * setPrefix
     *
     * @param mixed $prefix
     *
     * @access public
     * @return mixed
     */
    public function setPrefix($prefix)
    {
        $this->prefix = prefix;
    }

    /**
     * getPrefix
     *
     *
     * @access public
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFromUrl($url)
    {
        $parts = preg_split('~/~', $url, -1, PREG_SPLIT_NO_EMPTY);

        return implode('.', array_slice($parts, count($parts) >= 2 ? -2 : -1));
    }

    /**
     * {@inheritdoc}
     */
    public function createKey($src, $fingerprint = null, $prefix = 'io', $suffix = 'file')
    {
        return sprintf(
            '%s.%s%s%s',
            substr(hash('sha1', $src), 0, 8),
            $this->prefix,
            $this->pad($src, $fingerprint),
            $this->pad($src, $suffix, 3)
        );
    }

    /**
     * get
     *
     * @param mixed $id
     *
     * @return string
     */
    abstract public function get($id);

    /**
     * set
     *
     * @param string $id
     * @param Image $image
     *
     * @return void
     */
    abstract public function set($id, $content);

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->pool)) {
            return true;
        }
    }

    /**
     * getSource
     *
     *
     * @access public
     * @return void
     */
    public function getSource($id)
    {
        return isset($this->pool[$key]) ? $this->pool[$key] : $this->getPath($key);
    }

    /**
     * parseKey
     *
     * @param mixed $key
     *
     * @return array
     */
    protected function parseKey($key)
    {
        $path = strtr(substr($key, 0, ($pos = strpos($key, '.'))), ['.' => DIRECTORY_SEPARATOR]);
        $file = substr($key, $pos + 1);

        return [$path, $file];
    }

    /**
     * getPath
     *
     * @param string $id
     *
     * @return string
     */
    protected function getPath($id)
    {
    }

    /**
     * Appends and hash a string with another string.
     *
     * @param string $src
     * @param string $pad
     * @param int    $len
     *
     * @access protected
     * @return string
     */
    protected function pad($src, $pad, $len = 16)
    {
        return substr(hash('sha1', sprintf('%s%s', $src, $pad)), 0, $len);
    }

    /**
     * poolHas
     *
     * @param sring $id
     *
     * @return boolean
     */
    protected function poolHas($key)
    {
        return array_key_exists($key, $this->pool);
    }
}
