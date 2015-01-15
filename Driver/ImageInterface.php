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

use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;

/**
 * @interface ImageInterface
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImageInterface
{
    /**
     * coalesce
     *
     * May throw exception.
     *
     * @return ImageInterface
     */
    public function coalesce();

    /**
     * hasFrames
     *
     * @return boolean
     */
    public function hasFrames();

    /**
     * getWidth
     *
     *
     * @return int
     */
    public function getWidth();

    /**
     * getHeight
     *
     * @return int
     */
    public function getHeight();

    /**
     * newImage
     *
     * @param mixed $format
     *
     * @return ImageInterface
     */
    public function newImage($format = null);

    /**
     * getFormat
     *
     * @return string
     */
    public function getFormat();

    /**
     * setFormat
     *
     * @param mixed $format
     *
     * @return void
     */
    public function setFormat($format);

    /**
     * extent
     *
     * @param BoxInterface $size
     * @param PointInterface $start
     * @param mixed $color
     *
     * @return void
     */
    public function extent(BoxInterface $size, PointInterface $start = null, $color = null);

    /**
     * {@inheritdoc}
     */
    public function scale($perc);

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, $color = null);

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size);

    /**
     * {@inheritdoc}
     */
    public function crop(BoxInterface $size, PointInterface $crop = null);

    /**
     * {@inheritdoc}
     */
    public function frames();

    /**
     * {@inheritdoc}
     */
    public function get($format = null, array $options = []);
}
