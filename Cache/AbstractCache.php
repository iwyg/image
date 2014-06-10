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

/**
 * @abstract class AbstractCache implements CacheInterface
 * @see CacheInterface
 * @abstract
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * pool
     *
     * @var array
     */
    protected $pool;

    /**
     * prefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * suffix
     *
     * @var string
     */
    protected $suffix;

    /**
     * {@inheritdoc}
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuffix()
    {
        return $this->suffix;
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
    public function createKey($src, $fingerprint = null)
    {
        return sprintf(
            '%s.%s%s%s',
            substr(hash('sha1', $src), 0, 8),
            $this->prefix,
            $this->pad($src, $fingerprint),
            $this->pad($src, $this->suffix, 3)
        );
    }

    /**
     * {@inheritdoc}
     */
    abstract public function get($id, $raw = self::CONTENT_RESOURCE);

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * Hashes a string and a padd and returns a string with a given length.
     *
     * @param string $src
     * @param string $pad
     * @param int    $len
     *
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
