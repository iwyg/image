<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Im;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Driver\Im\Command\Raw;

/**
 * @class Grayscale
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends ImFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        if ($image->hasFrames()) {
            $image->frames()->coalesce();
        }

        $image->addCommand(new Raw('-modulate 100%,0%,100%'));
    }
}
