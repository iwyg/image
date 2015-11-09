<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Driver\AbstractFrames;
use Thapp\Image\Driver\Im\Command\Coalesce;

/**
 * @class Frames
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Frames extends AbstractFrames
{
    /** @var Image */
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
     */
    public function coalesce()
    {
        $this->image->addCommand(new Coalesce);

        return $this;
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
    public function remove($index)
    {
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
    public function count()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     *
     * Will always return the original Image object.
     *
     * @return Image
     */
    protected function getImageAt($index)
    {
        return $this->image;
    }
}
