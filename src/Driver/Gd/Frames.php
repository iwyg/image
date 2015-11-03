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

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Driver\AbstractFrames;
use Thapp\Image\Driver\FramesInterface;

/**
 * @class Frames
 *
 * @package Thapp\Image\Driver\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames extends AbstractFrames
{
    private $image;

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
     * @throws \LogicException
     */
    public function set($index, ImageInterface $image)
    {
        throw new \LogicException(sprintf('%s doesn\'t support multiple images', get_class($this)));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($index)
    {
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
    public function rewind()
    {
        $this->offset = 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function getImageAt($index)
    {
        return $this->image;
    }
}

