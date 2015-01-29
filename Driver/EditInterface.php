<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;

/**
 * @interface EditInterface
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EditInterface
{
    const COPY_DEFAULT = 'copy';
    const COPY_OVER    = 'over';
    const COPY_OVERLAY = 'overlay';

    /**
     * Resizes the image without affecting the image content size.
     *
     * @param BoxInterface $size
     * @param PointInterface $start
     * @param ColorInterface $color
     *
     * @return void
     */
    public function extent(BoxInterface $size, PointInterface $start = null, ColorInterface $color = null);

    /**
     * scale
     *
     * @param mixed $perc
     *
     * @return void
     */
    public function scale($perc);

    /**
     * rotate
     *
     * @param mixed $deg
     * @param ColorInterface $color
     *
     * @return void
     */
    public function rotate($deg, ColorInterface $color = null);

    /**
     * resize
     *
     * @param BoxInterface $size
     *
     * @return void
     */
    public function resize(BoxInterface $size);

    /**
     * crop
     *
     * @param BoxInterface $size
     * @param PointInterface $crop
     * @param ColorInterface $color
     *
     * @return void
     */
    public function crop(BoxInterface $size, PointInterface $crop = null, ColorInterface $color = null);

    /**
     * Like extent but ignores the images gravity setting.
     *
     * @param BoxInterface $size
     * @param PointInterface $point
     * @param ColorInterface $color
     *
     * @return void
     */
    public function canvas(BoxInterface $size, PointInterface $point, ColorInterface $color = null);

    /**
     * copy
     *
     * @param ImageInterface $image
     * @param PointInterface $start
     *
     * @return void
     */
    public function paste(ImageInterface $image, PointInterface $start = null);
}