<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Imagick;

use Imagick;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Colorize
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Colorize extends ImagickFilter
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
        $overlay = new Imagick();
        $overlay->newImage($image->getWidth(), $image->getHeight(), (string)$this->color);

        if ($image->hasFrames()) {
            foreach ($image->frames()->coalesce() as $frame) {
                $this->applyComposite($frame, $overlay);
            }
        } else {
            $this->applyComposite($image, $overlay);
        }
    }

    private function applyComposite(ImageInterface $image, Imagick $overlay)
    {
        $imagick = $image->getImagick();
        $imagick->modulateImage(102, 0, 100);
        $imagick->compositeImage($overlay, Imagick::COMPOSITE_MULTIPLY, 0, 0);
        $imagick->gammaImage(1.2);
    }
}
