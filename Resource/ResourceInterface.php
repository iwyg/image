<?php

/**
 * This File is part of the \Users\malcolm\www\image\src\Thapp\Image\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Resource;

/**
 * @interface ResourceInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface ResourceInterface
{
    /**
     * isLocal
     *
     * @return boolean
     */
    public function isLocal();

    /**
     * isFresh
     *
     * @return boolean
     */
    public function isFresh();

    /**
     * getContents
     *
     * @return string
     */
    public function getContents();

    /**
     * getMimeType
     *
     * @return string
     */
    public function getMimeType();

    /**
     * getPath
     *
     * @return string
     */
    public function getPath();
}
