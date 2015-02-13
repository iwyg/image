<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Color\Profile\ProfileInterface;

/**
 * @interface ImageInterface
 *
 * @package Thapp\Image
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

    const INTERLACE_NO = 0;
    const INTERLACE_LINE = 1;
    const INTERLACE_PLANE = 2;
    const INTERLACE_PARTITION = 2;

    /**
     * Destroy the image object.
     *
     * @return void
     */
    public function destroy();

    /**
     * Copy the image object.
     *
     * @return void
     */
    public function copy();

    /**
     * backup
     *
     * @param string $name
     *
     * @return void
     */
    public function backup($name = null);

    /**
     * restore
     *
     * @param string $name
     *
     * @return ImageInterface
     */
    public function restore($name = null);

    /**
     * Coalesce image frames.
     *
     * May throw exception.
     *
     * @return FramesInterface
     */
    public function coalesce();

    /**
     * Tell if the image has frames.
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
     * Get the current image size as a size object.
     *
     * @return \Thapp\Image\Geometry\SizeInterface
     */
    public function getSize();

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
     * filter
     *
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function filter(FilterInterface $filter);

    /**
     * Gets an edit object.
     *
     * @return Thapp\Image\Driver\EditInterface
     */
    public function edit();

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
    public function newImage($format = null, ColorInterface $backgound = null);

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
     * Get the image content as binary string.
     *
     * @param string $asFormat Image format, on of the ImageInterface::FORMAT_*
     * constants.
     *
     * @param array $options Output options
     *
     * - $format [string]
     * - $flatten [boolean]
     * - $interlace [int]
     * - $comperession_quality_png  [int] 0 - 100
     * - $comperession_quality_jpeg [int] 0 - 100
     * - $comperession_quality_gif  [int] 0 - 100
     * - $comperession_quality_tiff [int] 0 - 100
     *
     * @return string
     */
    public function getBlob($imageFormat = null, array $options = []);

    /**
     * Get the image metadata.
     *
     * @return Thapp\Image\Info\MetaDataInterface
     */
    public function getMetaData();

    /**
     * Get the color palette associated with the image.
     *
     * @return void
     */
    public function applyPalette(PaletteInterface $palette);
    public function applyProfile(ProfileInterface $profile);

    /**
     * Get the color palette associated with the image.
     *
     * @return Thapp\Image\Color\Palette\PaletteInterface
     */
    public function getPalette();

    /**
     * Strip all profiles and comments
     *
     * @return void
     */
    public function strip();
}
