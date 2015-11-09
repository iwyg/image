<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use LogicException;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\RgbPaletteInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Color\Profile\ProfileInterface;
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
    use GdHelper;

    /** @var resource */
    private $gd;

    /** @var string */
    private $sourceFormat;

    /** @var array */
    private static $interlaceMap = [
        self::INTERLACE_NO        => 0,
        self::INTERLACE_LINE      => 1,
        self::INTERLACE_PLANE     => 1,
        self::INTERLACE_PARTITION => 1,
    ];

    /**
     * Constructor.
     *
     * @param resource $resource GD Image resource
     * @param RgbPaletteInterface $palette
     * @param MetaDataInterface $meta
     */
    public function __construct($resource, RgbPaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->setResource($resource);
        $this->palette = $palette;
        $this->meta    = $meta ?: new MetaData([]);
        $this->frames  = new Frames($this);
    }

    /**
     * Prepares the image object when cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $this->gd     = $this->cloneGd();
        $this->meta   = clone $this->meta;
        $this->frames = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        if ($this->isValidResource($this->gd)) {
            imagedestroy($this->gd);
        }

        $this->gd = null;
        $this->frames = null;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        if (null === $this->format) {
            $this->format = $this->sourceFormat;
        }

        return $this->format;
    }

    /**
     * {@inheritdoc}
     * @throws LogicException will always throw as GD does not support color
     * profiles.
     */
    public function applyProfile(ProfileInterface $profile)
    {
        throw new LogicException('GD doesn\'t support profiles');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return imagesx($this->gd);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return imagesy($this->gd);
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null, ColorInterface $color = null)
    {
        $resource = $this->newFromGd($this->getSize(), $color);

        $image = new static($resource, $this->palette, clone $this->meta);
        $image->setSourceFormat($this->sourceFormat);

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getColorAt(PointInterface $pixel)
    {
        if (!$this->getSize()->has($pixel)) {
            throw new \OutOfBoundsException('Sample is outside of image.');
        }

        $rgb = imagecolorat($this->gd, $pixel->getX(), $pixel->getY());
        list ($r, $g, $b, $a) = array_values($color = imagecolorsforindex($this->gd, $rgb));
        $a = 1 - round(($a / 127), 2);

        return $this->palette->getColor([$r, $g, $b, $a]);
    }

    /**
     * Get the current GD resource
     *
     * @return resource
     */
    public function getGd()
    {
        return $this->gd;
    }

    /**
     * Swaps the current GD resource
     *
     * @param resource $resource GD image resource
     *
     * @return void
     */
    public function swapGd($resource)
    {
        $this->setResource($resource);
    }

    /**
     * Creates a new truecolorimage
     *
     * @internal
     * @param SizeInterface $size image size
     * @param ColorInterface $color image background
     *
     * @return resource A GD image resource
     */
    public function newGd(SizeInterface $size, ColorInterface $color = null)
    {
        return $this->newFromGd($size, $color);
    }

    /**
     * Set the file format of the source image.
     *
     * @internal
     *
     * @param string $format
     *
     * @return void
     */
    public function setSourceFormat($format)
    {
        $this->sourceFormat = $this->mapFormat($format);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlob($format = null, array $options = [])
    {
        $format = $format ?: $this->getOption($options, 'format', $this->getFormat());

        if (in_array($format, $fmts = ['png', 'gif'])) {
            imagealphablending($this->gd, false);
            imagesavealpha($this->gd, true);
        } elseif (in_array($this->sourceFormat, $fmts)) {
            // copy image to white background:
            $canvas = $this->newFromGd($this->getSize());
            imagefill($canvas, 0, 0, $this->getColorId($canvas, new Rgb(255, 255, 255)));
            imagecopy($canvas, $this->gd, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            $this->swapGd($canvas);
        }

        // interlace image:
        if (false !== ($scheme = $this->getOption($options, 'interlace', false))) {
            imageinterlace($this->gd, $this->getInterlaceScheme($scheme));
        }

        return $this->generateOutPut(array_merge(['format' => $format], $options));
    }

    /**
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function &getInterlaceMap()
    {
        return static::$interlaceMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsPalette(PaletteInterface $palette)
    {
        return $palette instanceof RgbPaletteInterface;
    }

    /**
     * Clone the existing GD resource
     *
     * @return resource
     */
    private function cloneGd()
    {
        $size = $this->getSize();
        $copy = $this->newFromGd($size);

        if (false === ($gd = imagecopy($copy, $this->gd, 0, 0, 0, 0, $size->getWidth(), $size->getHeight()))) {
            throw new ImageException('Cloning GD resource failed.');
        }

        return $gd;
    }

    /**
     * generateOutPut
     *
     * @param array $options
     *
     * @return string
     */
    private function generateOutPut(array $options)
    {
        if (false === $map = $this->mapOutputFormat($options)) {
            throw new ImageException(sprintf('Unsupported format "%s".', (string)$format));
        }

        list ($fn, $args) = $map;
        list ($path, ) = array_pad($args, 1, null);

        ob_start();
        try {
            $error = null;
            array_unshift($args, $this->gd);
            call_user_func_array($fn, $args);
        } catch (\Exception $e) {
            $error = new ImageException('Couldn\'t create image.', $e->getCode(), $e);
            ob_end_clean();
        }

        if (null !== $path) {
            unlink($path);
        }

        if ($error instanceof \Exception) {
            throw $error;
        }

        return ob_get_clean();
    }

    /**
     * Create a new GD resource from the existing one.
     *
     * @param SizeInterface $size
     * @param ColorInterface $color
     *
     * @return resource
     */
    private function newFromGd(SizeInterface $size, ColorInterface $color = null)
    {
        $gd = imagecreatetruecolor($w = $size->getWidth(), $h = $size->getHeight());
        $color = $color ?: $this->palette->getColor([255, 255, 255]);

        if (!(bool)imagealphablending($gd, false) || !(bool)imagesavealpha($gd, true)) {
            throw new \RuntimeException('Cannot create image.');
        }

        if (function_exists('imageantialias')) {
            imageantialias($this->gd, true);
        }

        imagefill($gd, 0, 0, $index = $this->getColorId($gd, $color));

        if (0.95 <= $color->getAlpha()) {
            imagecolortransparent($gd, $index);
        }

        return $gd;
    }

    /**
     * setResource
     *
     * @param resource $resource
     *
     * @return void
     */
    private function setResource($resource)
    {
        if (!$this->isValidResource($resource)) {
            throw new \InvalidArgumentException('Invalid resource.');
        }

        if (!imageistruecolor($resource)) {
            throw new \InvalidArgumentException(sprintf('%s only supports truecolor images.', get_class($this)));
        }

        if (is_resource($this->gd)) {
            imagedestroy($this->gd);
        }

        $this->gd = $resource;
    }

    /**
     * mapOutputFormat
     *
     * @param array $options
     *
     * @return array
     */
    private function mapOutputFormat(array $options)
    {
        switch ($options['format']) {
            case 'jpg':
            case self::FORMAT_JPEG:
                return $this->getJpegSaveArgs($options);
            case self::FORMAT_PNG:
                return $this->getPngSaveArgs($options);
            case self::FORMAT_GIF:
                return ['imagegif', []];
            case self::FORMAT_WBMP:
                return function_exists('imagewbmp') ? ['imagewbmp', []] : false;
            case self::FORMAT_WEBP:
                // webp and xbm need save paths
                return function_exists('imagewebp') ? ['imagewebp', [$this->getSaveTmpPath()]] : false;
            case self::FORMAT_XBM:
                return function_exists('imagexbm') ? ['imagexbm', [$this->getSaveTmpPath()]] : false;
            default:
                return false;
        }
    }

    /**
     * getSaveTmpPath
     *
     * @return string
     */
    private function getSaveTmpPath()
    {
        return tempnam(sys_get_temp_dir(), 'gd_'.time());
    }

    /**
     * getJpegSaveArgs
     *
     * @param array $options
     *
     * @return array
     */
    private function getJpegSaveArgs(array $options)
    {
        return ['imagejpeg', [null, min(100, max(0, $this->getOption($options, 'compression_quality_jpeg', 80)))]];
    }

    /**
     * getPngSaveArgs
     *
     * @param array $options
     *
     * @return array
     */
    private function getPngSaveArgs(array $options)
    {
        $compression = (9 - (int)round(9 * (min(100, max(0, $this->getOption($options, 'compression_quality_png', 50))) / 100)));

        return ['imagepng', [null, $compression]];
    }

    /**
     * isValidResource
     *
     * @param resource $gd
     *
     * @return bool
     */
    private function isValidResource($gd)
    {
        return is_resource($gd) && 'gd' === get_resource_type($gd);
    }
}
