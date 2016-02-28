<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Imagick;
use ImagickException;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Color\Profile\ProfileInterface;
use Thapp\Image\Color\Profile\Profile;
use Thapp\Image\Info\MetaData;
use Thapp\Image\Info\MetaDataInterface;
use Thapp\Image\Exception\ImageException;

/**
 * @class Image
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    use HelperTrait;

    /** @var string */
    private static $version;

    /** @var array */
    private static $typeMap;

    /** @var array */
    private static $interlaceMap = [
        self::INTERLACE_NO        => Imagick::INTERLACE_NO,
        self::INTERLACE_LINE      => Imagick::INTERLACE_LINE,
        self::INTERLACE_PLANE     => Imagick::INTERLACE_PLANE,
        self::INTERLACE_PARTITION => Imagick::INTERLACE_PARTITION
    ];

    /** @var array */
    private static $cspaceMap = [
        PaletteInterface::PALETTE_RGB       => Imagick::COLORSPACE_SRGB,
        PaletteInterface::PALETTE_CMYK      => Imagick::COLORSPACE_CMYK,
        PaletteInterface::PALETTE_GRAYSCALE => Imagick::COLORSPACE_GRAY,
    ];

    /** @var array */
    private static $orientMap = [
        Imagick::ORIENTATION_UNDEFINED   => self::ORIENT_UNDEFINED,
        Imagick::ORIENTATION_TOPLEFT     => self::ORIENT_TOPLEFT,
        Imagick::ORIENTATION_TOPRIGHT    => self::ORIENT_TOPRIGHT,
        Imagick::ORIENTATION_BOTTOMRIGHT => self::ORIENT_BOTTOMRIGHT,
        Imagick::ORIENTATION_BOTTOMLEFT  => self::ORIENT_BOTTOMLEFT,
        Imagick::ORIENTATION_LEFTTOP     => self::ORIENT_LEFTTOP,
        Imagick::ORIENTATION_RIGHTTOP    => self::ORIENT_RIGHTTOP,
        Imagick::ORIENTATION_RIGHTBOTTOM => self::ORIENT_RIGHTBOTTOM,
        Imagick::ORIENTATION_LEFTBOTTOM  => self::ORIENT_LEFTBOTTOM
    ];

    private static $colorSpaceMap = [
        Imagick::COLORSPACE_SRGB      => 'RGB',
        Imagick::COLORSPACE_RGB       => 'RGB',
        Imagick::COLORSPACE_CMYK      => 'CMYK',
        Imagick::COLORSPACE_GRAY      => 'GRAY',
        Imagick::COLORSPACE_UNDEFINED => 'UNDEFINED'
    ];

    /** @var Imagick */
    private $imagick;

    /**
     * Constructor.
     *
     * @param Imagick $imagick
     *
     * @return void
     */
    public function __construct(Imagick $imagick, PaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->imagick = $imagick;
        $this->frames  = new Frames($this);
        $this->meta    = $meta ?: new MetaData([]);

        $this->setImageColorspace($palette);

        $this->setVersion();
    }

    private function setVersion()
    {
        if (null !== static::$version) {
            return;
        }

        $version = phpversion('imagick');

        static::$version  = $version === '@PACKAGE_VERSION@' ? '3.4.0HEAD' : $version;
    }

    /**
     * Returns a copy of this image.
     *
     * @return void
     */
    public function __clone()
    {
        $this->imagick  = $this->cloneImagick();
        $this->meta     = clone $this->meta;
        $this->frames   = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        if (null === $this->imagick) {
            return false;
        }

        $this->imagick->clear();
        $this->imagick->destroy();
        $this->imagick = null;

        $this->frames = null;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * NOTE: If ImageMagick was not compiled with little-cms support,
     * this will do nothing.
     * Make shure the lcms delegate is available:
     * convert -list configure | grep DELEGATES
     */
    public function applyProfile(ProfileInterface $profile)
    {
        try {
            return $this->imagick->profileImage($profile->getName(), (string)$profile);
        } catch (ImagickException $e) {
            throw new ImageException(sprintf('Cannot set %s profile.', $profile->getName()), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyPalette(PaletteInterface $palette)
    {
        if (false === parent::applyPalette($palette)) {
            return false;
        }

        if (false === (boolean)$this->imagick->getImageProfiles('icc')) {
            $this->applyProfile($this->palette->getProfile());
        } else {
            $this->applyProfile(Profile::fromString('icc', $this->imagick->getImageProfile('icc')));
        }

        $this->setImageColorspace($palette, true);
        $this->applyProfile($palette->getProfile());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsPalette(PaletteInterface $palette)
    {
        return isset(static::$cspaceMap[$palette->getConstant()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAt(PointInterface $pixel)
    {
        if (!$this->getSize()->has($pixel)) {
            throw new \OutOfBoundsException('Sample is outside of image.');
        }

        return $this->colorFromPixel($this->imagick->getImagePixelColor($pixel->getX(), $pixel->getY()));
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrames()
    {
        return 1 < $this->imagick->getNumberImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->imagick->current()->getImageWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->imagick->current()->getImageHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        if (null === $this->format) {
            $this->format = $this->imagick->getImageFormat();
        }

        return parent::getFormat();
    }

    /**
     * {@inheritdoc}
     */
    public function getColorSpace()
    {
        if (isset(self::$colorSpaceMap[$id = $this->imagick->getColorspace()])) {
            return self::$colorSpaceMap[$id];
        };

        return 'undefined';
    }

    /**
     * {@inheritdoc}
     */
    public function getOrientation()
    {
        if (!$orient = $this->meta->get($key = 'ifd0.Orentation')) {
            $this->meta->set($key, $orient = static::$orientMap[$this->imagick->getImageOrientation()]);
        }

        return $this->mapOrientation($orient);
    }

    /**
     * {@inheritdoc}
     */
    public function strip()
    {
        try {
            $this->imagick->stripImage();
        } catch (ImagickException $e) {
            throw new ImageException('Cannot strip image data', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null, ColorInterface $color = null)
    {
        $imagick = new Imagick;
        $px = $color ? $color->getColorAsString() : $this->imagick->getImageBackgroundColor();
        $imagick->newImage($this->getWidth(), $this->getHeight(), $px);

        if (null === $format && $fmt = $this->getFormat()) {
            $format = $fmt;
        }

        if (null !== $format) {
            $imagick->setImageFormat($fmt);
        }

        $palette = $color ? $color->getPalette() : $this->palette;

        return new static($imagick, $palette, clone $this->meta);
    }

    /**
     * Get the current imagick resource
     *
     * @return Imagick
     */
    public function getImagick()
    {
        return $this->imagick;
    }

    /**
     * Swaps the current imagick resource.
     *
     * Will try to preserve the previous colorspace and profile.
     *
     * @param Imagick $imagick
     *
     * @return void
     */
    public function swapImagick(Imagick $imagick)
    {
        if (null !== $this->imagick) {
            if ($this->imagick->getColorspace() !== $imagick->getColorspace()) {
                //throw new ImageException('Cannot swap imagick, colospace missmatch.');
            }

            $map = array_flip(static::$orientMap);
            $orient = $map[$this->getOrientation()];

            try {
                $profile = $this->getPalette()->getProfile();
                //$imagick->profileImage($profile->getName(), (string)$profile);
                //$imagick->setColorspace($this->imagick->getImageColorSpace());
                //$canvas->setImageType($this->imagick()->getImageType());
            } catch (ImagickException $e) {
                throw new ImageException('Failed to set attributes imagick resource.', $e->getCode(), $e);
            }

            $this->imagick->clear();
            $this->imagick->destroy();

            try {
                $imagick->setImageOrientation($orient);
            } catch (ImagickException $orient) {
            }
        }

        $this->imagick = $imagick;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlob($format = null, array $options = [])
    {
        $format = $this->getOutputFormat($format, $options);

        if (!in_array($format, ['png', 'gif', 'tiff'])) {
            // Preserve color appearance on transparent images.
            // Setting the background color doesn't really work.
            // Instead copy the image to a white background.
            if ($this->isMatteImage($this->imagick)) {
                //$this->edit()->extent(
                //    $this->getSize(),
                //    new Point(0, 0),
                //    $this->palette->getColor([255, 255, 255, 1])
                //);
                $this->flatten();
            }
        }

        if ($this->hasFrames()) {
            $this->frames()->merge();

            if ($this->getOption($options, 'flatten', false)) {
                $this->flatten();
            }
        }

        try {
            $this->applyOptions($options);
        } catch (\Exception $e) {
            throw ImageException::output($e);
        }

        return $this->imagick->getImagesBlob();
    }

    /**
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * &getInterlaceMap
     *
     * @return void
     */
    protected function &getInterlaceMap()
    {
        return static::$interlaceMap;
    }

    /**
     * cloneImagick
     *
     * @return void
     */
    private function cloneImagick()
    {
        if (version_compare(static::$version, '3.1.0b1', '>=') || defined('HHVM_VERSION')) {
            return clone $this->imagick;
        }

        return $this->imagick->clone();
    }

    /**
     * flatten
     *
     * @return void
     */
    private function flatten()
    {
        try {
            $this->imagick = $this->imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        } catch (ImagickException $e) {
            throw new ImageException('Cannot flatten image', $e->getCode(), $e);
        }
    }

    /**
     * setImageColorspace
     *
     * @param PaletteInterface $palette
     * @param mixed $switch
     *
     * @return void
     */
    private function setImageColorspace(PaletteInterface $palette, $switch = false)
    {
        $cs = $this->imagick->getColorspace();

        try {
            $this->imagick->setColorspace(static::$cspaceMap[$palette->getConstant()]);
        } catch (ImagickException $e) {
            throw new ImagickException('setting colorspace failed.', $e->getCode(), $e);
        }

        if (false !== $switch && $cs === $this->imagick->getColorspace()) {
            throw new ImageException('Changing coloespace failed. Make shure ImageMagick has little-cms support.');
        }

        $this->palette = $palette;
    }

    /**
     * setImageType
     *
     * @param PaletteInterface $palette
     *
     * @return void
     */
    private function setImageType(PaletteInterface $palette)
    {
        $map = $this->getTypeMap();
        if (!isset($map[$palette->getConstant()])) {
            throw new ImageException('Colorspace is not supported.');
        }

        $this->imagick->setType($map[$palette->getConstant()]);
    }

    /**
     * getTypeMap
     *
     * @return void
     */
    private function getTypeMap()
    {
        if (null === static::$typeMap) {
            static::$typeMap = array_combine(
                array_keys(static::$cspaceMap),
                [
                    Imagick::IMGTYPE_TRUECOLORMATTE,
                    Imagick::IMGTYPE_TRUECOLOR,
                    Imagick::IMGTYPE_GRAYSCALEMATTE,
                ]
            );
        }

        return static::$typeMap;
    }

    /**
     * applyOptions
     *
     * @param array $options
     *
     * @return void
     */
    private function applyOptions(array $options)
    {
        if (false !== $scheme = $this->getOption($options, 'interlace', false)) {
            $this->imagick->setInterlaceScheme($this->getInterlaceScheme($scheme));
        }

        $this->imagick->setImageFormat($options['format']);

        // set compression
        if (self::FORMAT_PNG === $options['format']) {
            $this->imagick->setImageCompression(Imagick::COMPRESSION_NO);
            $this->imagick->setImageCompressionQuality(
                min(100, max(0, $this->getOption($options, 'compression_quality_png', 50)))
            );
        } elseif (self::FORMAT_JPEG === $options['format']) {
            $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
            $this->imagick->setImageCompressionQuality(
                min(100, max(0, $this->getOption($options, 'compression_quality_jpeg', 80)))
            );
        } elseif (self::FORMAT_GIF === $options['format']) {
            $this->imagick->setImageCompression(
                defined('Imagick::COMPRESSION_GIF') ? Imagick::COMPRESSION_GIF : Imagick::COMPRESSION_NO
            );
            $this->imagick->setImageCompressionQuality(
                min(100, max(0, $this->getOption($options, 'compression_quality_gif', 80)))
            );
        } elseif (self::FORMAT_TIFF === $options['format']) {
            $this->imagick->setImageCompression(Imagick::COMPRESSION_LZW);
            $this->imagick->setImageCompressionQuality(
                min(100, max(0, $this->getOption($options, 'compression_quality_tiff', 80)))
            );
        }
    }
}
