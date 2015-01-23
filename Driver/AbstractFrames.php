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
 * @class AbstractFrames
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractFrames implements FramesInterface
{

    /**
     * {@inheritdoc}
     */
    public function get($index)
    {
        return $this->getImageAt($index);
    }

    /**
     * {@inheritdoc}
     */
    public function set($index, ImageInterface $image)
    {
        $this->frames[$index] = $image;
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
    public function next()
    {
        $this->offset++;
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
        return $this->getImageAt($this->offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($index)
    {
        $this->remove($index);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($index)
    {
        return $index > 0 && $index < $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($index, $value)
    {
        $this->set($index, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($index)
    {
        return $this->get($index);
    }

    /**
     * getImageAt
     *
     * @param mixed $index
     *
     * @return void
     */
    abstract protected function getImageAt($index);
}
