<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

use Thapp\Image\Driver\ImageInterface;

/**
 * @class AutoRotate
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class AutoRotate implements FilterInterface
{
    /**
     * Autorotates an image according to its EXIF Orinentation value.
     */
    public function apply(ImageInterface $image)
    {
        if (ImageInterface::ORIENT_UNDEFINED === ($orient = $image->getOrientation())) {
            return;
        }

        switch ($orient) {
            case ImageInterface::ORIENT_BOTTOMRIGHT:
                $image->rotate(180);
                break;
            case ImageInterface::ORIENT_RIGHTTOP:
                $image->rotate(90);
                break;
            case ImageInterface::ORIENT_LEFTBOTTOM:
                $image->rotate(-90);
                break;
            default:
                break;
        }
    }
}
