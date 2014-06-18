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

use \Thapp\Image\ProcessorInterface;

/**
 * @interface CacheInterface
 * @package Thapp\Image\Cache
 * @version $Id$
 */
interface CacheInterface
{
    const CONTENT_STRING   = true;

    const CONTENT_RESOURCE = false;

    /**
     * get
     *
     * @param string $key
     * @param boolean $raw
     *
     * @return string|\Thapp\Immage\Resource\ResourceInterface
     */
    public function get($key, $raw = self::CONTENT_RESOURCE);

    /**
     * getSource
     *
     * @return string
     */
    public function getSource($id);

    /**
     * set
     *
     * @param string $key
     * @param ProcessorInterface $proc
     *
     * @return void
     */
    public function set($key, ProcessorInterface $proc);

    /**
     * has
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Delete the whole cache
     *
     * @return void
     */
    public function purge();

    /**
     * delete a cached group based in the image name.
     *
     * @param string $image
     *
     * @return void
     */
    public function delete($image);

    /**
     * setPrefix
     *
     * @param string $prefix
     *
     * @return void
     */
    public function setPrefix($prefix);

    /**
     * getPrefix
     *
     * @return string
     */
    public function getPrefix();

    /**
     * createKey
     *
     * @param string $src
     * @param string $fingerprint
     * @param string  $prefix
     * @param string  $suffix
     *
     * @access public
     * @return mixed
     */
    public function createKey($src, $fingerprint = null);
}
