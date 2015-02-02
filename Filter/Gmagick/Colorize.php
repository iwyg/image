<?php

/*
 * This File is part of the Thapp\Image\Filter\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Gmagick;

use Gmagick;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Colorize
 *
 * @package Thapp\Image\Filter\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Colorize extends GmagickFilter
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
        $overlay = new Gmagick();
        $overlay->newImage($image->getWidth(), $image->getHeight(), (string)$this->color);

        if ($image->hasFrames()) {
            foreach ($image->frames()->coalesce() as $frame) {
                $this->applyComposite($frame, $overlay);
            }
        } else {
            $this->applyComposite($image, $overlay);
        }

    }

    private function applyComposite(ImageInterface $image, Gmagick $overlay)
    {
        $gmagick = $image->getGmagick();
        $gmagick->modulateImage(102, 0, 100);
        $gmagick->compositeImage($overlay, Gmagick::COMPOSITE_MULTIPLY, 0, 0);
        $gmagick->gammaimage(1.2);
    }
}
