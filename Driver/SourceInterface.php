<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

/**
 * @interface SourceInterface
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SourceInterface
{
    /**
     * read
     *
     * @param resource $resource valid file handle or resource
     *
     * @return ImageInterface
     */
    public function read($resource);
}
