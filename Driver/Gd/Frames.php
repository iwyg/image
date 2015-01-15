<?php

/*
 * This File is part of the Thapp\Image\Driver\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use Thapp\Image\Driver\FramesInterface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames implements FramesInterface
{
    private $image;
    private $offset;

    /**
     * Constructor.
     *
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->offset = 0;
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->offset < $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->offset++;
    }
}
