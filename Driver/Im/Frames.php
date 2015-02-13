<?php

/*
 * This File is part of the Thapp\Image\Driver\Im package
 *
 * (c)  <>
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
 * @package Thapp\Image\Driver\Im
 * @version $Id$
 * @author  <>
 */
class Frames extends AbstractFrames
{
    private $image;

    public function __construct(Image $image)
    {
        $this->offset = 0;
        $this->image = $image;
    }

    public function coalesce()
    {
        $this->image->addCommand(new Coalesce);

        return $this;
    }

    public function merge()
    {
    }

    public function remove($index)
    {
    }

    public function rewind()
    {
        $this->offset = 0;
    }

    public function count()
    {
        return 1;
    }

    protected function getImageAt($index)
    {
        return $this->image;
    }
}
