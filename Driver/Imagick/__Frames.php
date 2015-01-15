<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Thapp\Image\Driver\ImageInteface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames implements \Countable, \Iterator, \ArrayAccess
{
    private $image;
    private $offset;
    private $frames;

    /**
     * Constructor.
     *
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
        $this->frames = [];
        $this->offset = 0;
    }

    public function merge()
    {
        foreach ($this as $index => $image) {
            $this->image->getImagick()->setIteratorIndex($index);
            $this->image->getImagick()->setImage($image->getImagick());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        //try {
            //$coalesce = $this->image->getImagick()->coalesceImages();
            ////$coalesce->rewind();

            //do {
                //$this->frames[$coalesce->getIteratorIndex()] = new Image($current = $coalesce->getImage());
            //} while ($coalesce->nextImage());
        //} catch (\ImagickException $e) {
            //throw $e;
        //}

        $coalesce = $this->image->getImagick()->coalesceImages();
        $this->image->swapImagick($coalesce->deconstructImages());
    }

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
    public function remove($index)
    {
        try {
            $this->image->getImagick()->setIteratorIndex($index);
            $this->image->removeImage();
        } catch (\ImagickException $e) {
            throw $e;
        }

        unset($this->frames[$index]);
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
        $this->image->getImagick()->setFirstIterator();
        $this->offset = 0;
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
    public function count()
    {
        return $this->image->getImagick()->getNumberImages();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $this->image->getImagick()->setIteratorIndex($this->offset);

        return $this->image;
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
    protected function getImageAt($index)
    {
        if (!isset($this->frames[$index])) {
            $im = $this->image->getImagick();
            $im->setIteratorIndex($index);
            $this->frames[$index] = new Image($im->getImage());
        }

        return $this->frames[$index];
    }
}
