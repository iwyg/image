<?php

/*
 * This File is part of the Thapp\Image\Driver\gmagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gmagick;

use Gmagick;
use Thapp\Image\Driver\ImageInteface;
use Thapp\Image\Metrics\GravityInterface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\gmagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames implements \Countable, \Iterator, \ArrayAccess
{
    private $image;
    private $offset;
    private $frames;
    private $coalesce;

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
        $this->coalesce = method_exists($this->image->getGmagick(), 'coalesceImages');
    }

    public function __destruct()
    {
        $this->image = null;
        $this->frames = [];
    }

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
        $resource = $this->image->getGmagick();
        $resource->setImageIndex(0);

        foreach (range(0, $this->count() - 1) as $index) {
            $image = $this->getImageAt($index);
            $resource->nextImage();
            $resource->setImageIndex($index);
            $resource->setImage($image->getGmagick());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        // merge previous frames
        $this->merge();

        $gmagick = $this->image->getGmagick();

        if ($this->supportsCoalesce()) {
            var_dump('supports coalesce.');
            $coalesce = $gmagick->coalesceImages();
        } else {
            var_dump('does not support coalesce.');
            $coalesce = clone $gmagick;
        }

        $gmagick->setImageIndex(0);

        do {
            $index = $coalesce->getImageIndex();
            $this->setFrame($coalesce->getImageIndex(), $coalesce->getImage());
        } while ($coalesce->nextImage());

        return $this;
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
            $this->image->getGmagick()->setImageIndex($index);
            $this->image->removeImage();
        } catch (\GmagickException $e) {
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
        $this->image->getGmagick()->setImageIndex(0);
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
        // getNumberImages is not documentet.
        // @see http://php.net/manual/en/book.gmagick.php
        try {
            return $this->image->getGmagick()->getNumberImages();
        } catch (\GmagickException $e) {
            return 1;
        }
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
    protected function getImageAt($index)
    {
        if (!isset($this->frames[$index])) {
            $im = $this->image->getGmagick();
            $im->setImageIndex($index);
            $this->setFrame($index, $im->getImage());
        }

        return $this->frames[$index];
    }

    /**
     * setFrame
     *
     * @param mixed $index
     * @param Gmagick $image
     *
     * @return void
     */
    protected function setFrame($index, Gmagick $image)
    {
        $this->frames[$index] = new Image($image);

        $gravity = $this->image->getGravity();

        if (GravityInterface::GRAVITY_NORTHWEST !== $gravity->getMode()) {
            $this->frames[$index]->gravity($gravity);
        }
    }

    /**
     * supportsCoalesce
     *
     * @return boolean
     */
    private function supportsCoalesce()
    {
        return $this->coalesce;
    }

}
