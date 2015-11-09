<?php

/*
 * This File is part of the Thapp\Image package
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
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SourceInterface
{
    /**
     * Create a new Image instance from a file resource
     *
     * @param resource $resource valid file handle or resource
     * @throws \Thapp\Image\Exception\ImageException if reading the image
     * resource fails.
     *
     * @return \Thapp\Image\Driver\ImageInterface
     */
    public function read($resource);

    /**
     * Create a new Image instance from a filename
     *
     * @param string $file image file
     * @throws \Thapp\Image\Exception\ImageException if loading the image fails.
     *
     * @return \Thapp\Image\Driver\ImageInterface
     */
    public function load($file);

    /**
     * Create a new Image from a binary blob
     *
     * @param string $content image blob
     * @throws \Thapp\Image\Exception\ImageException if creating the image
     * fails.
     *
     * @return \Thapp\Image\Driver\ImageInterface
     */
    public function create($image);
}
