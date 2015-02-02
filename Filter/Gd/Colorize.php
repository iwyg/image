<?php

/*
 * This File is part of the Thapp\Image\Filter\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Gd;

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Colorize
 *
 * @package Thapp\Image\Filter\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Colorize extends GdFilter
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
        $gd = $image->getGd();

        imagefilter($gd, IMG_FILTER_GRAYSCALE);
        imagefilter($gd, IMG_FILTER_CONTRAST, 27);
        imagefilter($gd, IMG_FILTER_BRIGHTNESS, -42);
        imagegammacorrect($gd, 1.0, 0.8);

        $this->applyComposite($image, $gd);
    }

    private function applyComposite(ImageInterface $image, $gd)
    {
        $r = $this->color->getRed();
        $g = $this->color->getGreen();
        $b = $this->color->getBlue();

        $width = $image->getWidth();
        $height = $image->getHeight();

        imagelayereffect($gd, IMG_EFFECT_OVERLAY);
        imagefilledrectangle($gd, 0, 0, $width, $height, imagecolorallocatealpha($gd, $r, $g, $b, 0));
        //$overlay = $image->newGd($image->getSize());
        //imagefilledrectangle($overlay, 0, 0, $width, $height, imagecolorallocatealpha($gd, $r, $g, $b, 1));
        //imagecopy($gd, $overlay, 0, 0, 0, 0, $width, $height);
        //imagedestroy($overlay);
    }
}
