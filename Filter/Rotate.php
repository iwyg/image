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

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Rotate
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rotate Implements FilterInterface
{
    private $deg;
    private $color;

    /**
     * Constructor.
     *
     * @param float $deg
     * @param ColorInterface $backgroud
     */
    public function __construct($deg = null, ColorInterface $backgroud = null)
    {
        $this->deg = (float)$deg;
        $this->color = $backgroud;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        if ($image->hasFrames()) {
            foreach ($image->frames()->coalesce() as $frame) {
                $frame->rotate($this->deg, $this->color);
            }
        } else {
            $image->rotate($this->deg, $this->color);
        }
    }
}
