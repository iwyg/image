<?php

/*
 * This File is part of the Thapp\Image\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

use Thapp\Image\Driver\ImageInterface;

/**
 * @class Flip
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Flip implements FilterInterface
{
    use FilterHelperTrait;

    const FLIP_VERTICAL = 0;
    const FLIP_HORIZONTAL = 1;
    const FLIP_BOTH = 2;

    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFilter(ImageInterface $image)
    {
        if (self::FLIP_VERTICAL === $this->mode) {
            $image->edit()->flip();
        } elseif (self::FLIP_HORIZONTAL === $this->mode) {
            $image->edit()->flop();
        } elseif (self::FLIP_BOTH === $this->mode) {
            $image->edit()->flip();
            $image->edit()->flop();
        }
    }
}
