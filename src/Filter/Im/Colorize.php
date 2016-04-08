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

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Driver\Im\Command\Raw;

/**
 * @class Colorize
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Colorize extends ImFilter
{
    private $color;

    /**
     * Constructor.
     *
     * @param ColorInterface $color
     */
    public function __construct(ColorInterface $color)
    {
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        if ($image->hasFrames()) {
            $image->frames()->coalesce();
        }

        $image->addCommand(
            new Raw(
                sprintf(
                    '( +clone -fill %s -colorize 100 ) -compose Colorize -composite',
                    $this->color->getColorAsString()
                )
            )
        );
    }
}
