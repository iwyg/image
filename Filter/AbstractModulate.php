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

/**
 * @trait AbstractGreyscale
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait AbstractModulate
{
    public function __construct($brightnes = 100, $saturation = 0, $hue = 100, $contrast = true)
    {
        $this->brightnes = $brightnes;
        $this->hue = $hue;
        $this->saturation = $saturation;
        $this->contrast = (bool)$contrast;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image, array $options = [])
    {
        if ($image->hasFrames()) {
            foreach ($image->frames()->coalesce() as $frame) {
                $this->applyModulate($frame, $this->brightnes, $this->saturation, $this->hue, $this->contrast);
            }
        } else {
            $this->applyModulate($image, $this->brightnes, $this->saturation, $this->hue, $this->contrast);
        }
    }

    /**
     * applyModulate
     *
     * @param ImageInterface $image
     * @param mixed $brightnes
     * @param mixed $saturation
     * @param mixed $hue
     * @param mixed $contrast
     *
     * @return void
     */
    abstract protected function applyModulate(ImageInterface $image, $brightnes, $saturation, $hue, $contrast);
}
