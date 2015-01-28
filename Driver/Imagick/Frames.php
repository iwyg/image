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

use Imagick;
use Thapp\Image\Driver\ImageInteface;
use Thapp\Image\Driver\AbstractFrames;
use Thapp\Image\Metrics\GravityInterface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames extends AbstractFrames implements \Countable, \Iterator, \ArrayAccess
{
    private $image;

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

    /**
     * {@inheritdoc}
     */
    public function merge()
    {
        $resource = $this->image->getImagick();
        $resource->setFirstIterator();

        foreach (range(0, $this->count() - 1) as $index) {
            $image = $this->getImageAt($index);
            $resource->nextImage();
            $resource->setIteratorIndex($index);
            $resource->setImage($image->getImagick());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        // merge previous frames
        $this->merge();

        $imagick  = $this->image->getImagick();
        $coalesce = $imagick->coalesceImages();
        $imagick->setFirstIterator();

        do {
            $index = $coalesce->getIteratorIndex();
            $this->setFrame($coalesce->getIteratorIndex(), $coalesce->getImage());
        } while ($coalesce->nextImage());

        return $this;
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
    public function rewind()
    {
        $this->image->getImagick()->rewind();
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->image->getImagick()->getNumberImages();
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
    protected function setFrame($index, Imagick $image)
    {
        $this->frames[$index] = new Image($image, $this->image->getPalette(), $this->image->getMetaData());

        $gravity = $this->image->getGravity();

        if (GravityInterface::GRAVITY_NORTHWEST !== $gravity->getMode()) {
            $this->frames[$index]->gravity($gravity);
        }
    }
}
