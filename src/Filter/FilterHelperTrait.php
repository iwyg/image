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
 * @trait FilterHelperTrait
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait FilterHelperTrait
{

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image, array $options = [])
    {
        if ($image->hasFrames()) {
            foreach ($image->frames()->coalesce() as $frame) {
                $this->applyFilter($frame);
            }
        } else {
            $this->applyFilter($image);
        }
    }

    /**
     * applyFilter
     *
     * @param ImageInterface $image
     *
     * @return void
     */
    abstract protected function applyFilter(ImageInterface $image);
}
