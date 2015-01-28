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
    const FORMAT_JPEG = 'jpeg';
    const FORMAT_PNG  = 'png';
    const FORMAT_GIF  = 'gif';
    const FORMAT_TIFF = 'tiff';
    const FORMAT_WBMP = 'wbmp';
    const FORMAT_WEBP = 'webp';
    const FORMAT_XBM  = 'xbm';

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

    const ORIENT_UNDEFINED = 0;
    const ORIENT_TOPLEFT = 1;
    const ORIENT_TOPRIGHT = 2;
    const ORIENT_BOTTOMRIGHT = 3;
    const ORIENT_BOTTOMLEFT = 4;
    const ORIENT_LEFTTOP = 5;
    const ORIENT_RIGHTTOP = 6;
    const ORIENT_RIGHTBOTTOM = 7;
    const ORIENT_LEFTBOTTOM = 8;

    /**
     * desctroy
     *
     * @return void
     */
    public function destroy();

    /**
     * destroy
     *
     * @return void
     */
    public function copy();

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
     * Get the color for a specified pixel
     *
     * @param PointInterface $pixel
     *
     * @return ColorInterface
     */
    public function getColorAt(PointInterface $pixel);

    /**
     * Get the image output format.
     *
     * @return string one of the ImageInterface::FORMAT_* constants
     */
    public function getFormat();

    /**
     * Get the imageorientation
     *
     * @return int one of the ImageInterface::ORIENT_* constants
     */
    public function getOrientation();

    /**
     * Get image frames
     *
     * @return FramesInterface
     */
    public function frames();

    /**
     * Set the image output format.
     *
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format);

    /**
     * Creates a new ImageInterface instance
     *
     * @param string $format
     * @param ColorInterface $color
     *
     * @return ImageInterface
     */
    public function newImage($format = null, ColorInterface $color = null);

    /**
     * Get the image gravity.
     *
     * @param GravityInterface $gravity
     *
     * @return void
     */
    public function setGravity(GravityInterface $gravity);

    /**
     * Get the image gravity.
     *
     * @return GravityInterface
     */
    public function getGravity();

    /**
     * @deprecated use ImageInterface::getBlob() instead.
     *
     * @param string $format
     * @param array $options
     *
     * @return string
     */
    public function get($format = null, array $options = []);

    /**
     * Get the image content as binary string.
     *
     * @param string $asFormat Image format, on of the ImageInterface::FORMAT_*
     * constants.
     *
     * @param array $options Output options
     *
     * @return string
     */
    public function getBlob($imageFormat = null, array $options = []);

    /**
     * getMetaData
     *
     * @return mixed
     */
    public function getMetaData();

    /**
     * getPalette
     *
     * @return PaletteInterface
     */
    public function getPalette();
}
