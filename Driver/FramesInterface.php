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
 * @interface FramesInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FramesInterface extends \Countable, \Iterator
{
    /**
     * merge
     *
     * @return void
     */
    public function merge();

    /**
     * coalesce
     *
     * @return FramesInterface
     */
    public function coalesce();

    /**
     * set
     *
     * @param int $index
     * @param ImageInterface $image
     *
     * @return void
     */
    public function set($index, ImageInterface $image);

    /**
     * get
     *
     * @param int $index
     *
     * @return ImageInterface
     */
    public function get($index);

    /**
     * remove
     *
     * @param int $index
     *
     * @return void
     */
    public function remove($index);
}
