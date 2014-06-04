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

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;

        parent::__construct();
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
            $prefix,
            $this->pad($src, $fingerprint),
            $this->pad($src, $suffix, 3)
        );
    }

    /**
     * setFromProcessor
     *
     * @param mixed $id
     * @param ProcessorInterface $processor
     *
     * @return void
     */
    public function setFromProcessor($id, ProcessorInterface $processor)
    {

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
     * @param mixed $key
     *
     * @access public
     * @return mixed
     */
    public function getSource($key)
    {
        return $this->pool[$key];
    }

    /**
     * parseKey
     *
     * @param mixed $key
     *
     * @access protected
     * @return void
     */
    protected function parseKey($key)
    {
        $path = strtr(substr($key, 0, ($pos = strpos($key, '.'))), ['.' => DIRECTORY_SEPARATOR]);
        $file = substr($key, $pos + 1);

        return [$path, $file];
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
}
