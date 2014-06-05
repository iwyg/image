<?php

/**
 * This File is part of the Thapp\JitImage package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Resource;

/**
 * @abstract class AbstractResource
 *
 * @package Image\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * contents
     *
     * @var string
     */
    protected $contents;

    /**
     * lastModified
     *
     * @var int
     */
    protected $lastModified;

    /**
     * mimeType
     *
     * @var mixed
     */
    protected $mimeType;

    /**
     * path
     *
     * @var string
     */
    protected $path;

    /**
     * isLocal
     *
     * @return boolean
     */
    public function isLocal()
    {
        return null !== $this->path && is_file($this->path);
    }

    /**
     * isFresh
     *
     * @access public
     * @return void
     */
    public function isFresh($time = null)
    {
        return $this->getLastModified() < $time ?: time();
    }

    /**
     * setContents
     *
     * @param string $contents
     *
     * @return void
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * getContents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * setFileMtime
     *
     * @param int $time
     *
     * @return void
     */
    public function setLastModified($time)
    {
        $this->lastModified = $time;
    }

    /**
     * getFileMtime
     *
     * @return int
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * setMimeType
     *
     * @param string $type
     *
     * @return void
     */
    public function setMimeType($type)
    {
        $this->mimeType = $type;
    }

    /**
     * getMimeTyoe
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * setPath
     *
     * @param string $path
     *
     * @return mixed
     */
    public function setPath($path)
    {

    }

    /**
     * getPath
     *
     * @return void
     */
    public function getPath()
    {
        return $this->path;
    }
}
