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
use Thapp\Image\Driver\AbstractFrames;
use Thapp\Image\Geometry\GravityInterface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\gmagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames extends AbstractFrames
{
    private $image;
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
        if (0 === $this->count()) {
            return;
        }

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
        if (1 < $this->count()) {
            // merge previous frames
            $this->merge();

            $gmagick = $this->image->getGmagick();

            if ($this->supportsCoalesce()) {
                $coalesce = $gmagick->coalesceImages();
            } else {
                $coalesce = clone $gmagick;
            }

            $gmagick->setImageIndex(0);

            do {
                $index = $coalesce->getImageIndex();
                $this->setFrame($coalesce->getImageIndex(), $coalesce->getImage());
            } while ($coalesce->nextImage());
        }

        return $this;
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
    public function rewind()
    {
        $this->image->getGmagick()->setImageIndex(0);
        $this->offset = 0;
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
        $this->frames[$index] = new Image($image, $this->image->getPalette(), $this->image->getMetaData());

        $gravity = $this->image->getGravity();

        if (GravityInterface::GRAVITY_NORTHWEST !== $gravity->getMode()) {
            $this->frames[$index]->setGravity($gravity);
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
