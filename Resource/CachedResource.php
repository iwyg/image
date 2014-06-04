<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Resource;

/**
 * @class CachedResource extends AbstractResource
 * @see AbstractResource
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class CachedResource extends AbstractResource
{
    /**
     * @param string $path
     * @param string $contents
     * @param int $lastModified
     * @param string $mime
     */
    public function __construct($path = null, $contents = '', $lastModified = 0, $mime = 'application/octet-stream')
    {
        $this->path = $path;
        $this->contents = $contents;
        $this->lastModified = $lastModified;
        $this->mimeType = $mime;
    }

    /**
     * getContents
     *
     * @return string
     */
    public function getContents()
    {
        if (is_callable($this->contents)) {
            return $this->contents = call_user_func($this->contents);
        }

        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function setContents($contents)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setLastModified($time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setMimeType($type)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
    }
}
