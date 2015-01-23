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
use Thapp\Image\Color\ColorInterface;

/**
 * @interface ImageInterface
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImageInterface
{
    const FILTER_UNDEFINED = 0;
    const FILTER_POINT = 1;
    const FILTER_BOX = 2;
    const FILTER_TRIANGLE = 3;
    const FILTER_HERMITE = 4;
    const FILTER_HANNING = 5;
    const FILTER_HAMMING = 6;
    const FILTER_BLACKMAN = 7;
    const FILTER_GAUSSIAN = 8;
    const FILTER_QUADRATIC = 9;
    const FILTER_CUBIC = 10;
    const FILTER_CATROM = 11;
    const FILTER_MITCHELL = 12;
    const FILTER_LANCZOS = 13;
    const FILTER_BESSEL = 14;
    const FILTER_SINC = 15;

    const FORMAT_JPEG = 'jpeg';
    const FORMAT_PNG = 'png';
    const FORMAT_GIF = 'png';
    const FORMAT_TIFF = 'tiff';
    const FORMAT_WEBP = 'webp';
    const FORMAT_XMB = 'xmb';

    /**
     * desctroy
     *
     * @return void
     */
    public function destroy();

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
     * Gets the current width
     *
     * @return int
     */
    public function getWidth();

    /**
     * Gets the current height
     *
     * @return int
     */
    public function getHeight();

    /**
     * Creates a new ImageInterface instance
     *
     * @param string $format
     *
     * @return void
     */
    public function newImage($format = null);

    /**
     * Get the image output format.
     *
     * @return string
     */
    public function getFormat();

    /**
     * Set the image output format.
     *
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format);

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
     * frames
     *
     *
     * @return void
     */
    public function frames();

    /**
     * gravity
     *
     * @param GravityInterface $gravity
     *
     * @return void
     */
    public function gravity(GravityInterface $gravity);

    /**
     * {@inheritdoc}
     */
    public function get($format = null, array $options = []);
}
