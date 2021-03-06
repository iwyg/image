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

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\Palette\RgbPaletteInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\GdFilter;
use Thapp\Image\Info\MetaData;
use Thapp\Image\Info\MetaDataInterface;

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

    private $gd;
    private $sourceFormat;

    /**
     * Constructor.
     *
     * @param resource $resource GD Image resource
     */
    public function __construct($resource, RgbPaletteInterface $palette, MetaDataInterface $meta = null)
    {
        $this->setResource($resource);
        $this->palette = $palette;
        $this->meta = $meta ?: new MetaData([]);
        $this->frames = new Frames($this);
    }

    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->gd = $this->cloneGd();
        $this->meta = clone $this->meta;
        $this->frames = new Frames($this);
    }

    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        return clone $this;
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
     * getGd
     *
     * @return resource
     */
    public function getGd()
    {
        return $this->gd;
    }

    /**
     * swapGd
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
     * setSourceFormat
     *
     * @internal
     * @param mixed $format
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
            //$canvas = $this->newFromGd($this->getSize());
            //imagefill($canvas, 0, 0, $this->getColorId($canvas, new Rgb(255, 255, 255)));
            //imagecopy($canvas, $this->gd, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
            //$this->swapGd($canvas);
        }

        return $this->generateOutPut($format);
    }

    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * copyGd
     *
     * @return void
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
     * @param string $fn
     *
     * @return string
     */
    private function generateOutPut($format)
    {
        if (!is_callable($fn = $this->mapOutputFormat($format))) {
            throw new ImageException(sprintf('Unsupported format "%s".', (string)$format));
        }

        $path = null;

        if (in_array($fn, ['imagewebp', 'imagexbm'])) {
            $path = tempnam(sys_get_temp_dir(), time());
        }

        ob_start();
        call_user_func($fn, $this->gd, $path);

        if (null !== $path) {
            unlink($path);
        }

        return ob_get_clean();
    }

    /**
     * newFromGd
     *
     * @param SizeInterface $size
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
     * @param mixed $resource
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
     * @param mixed $fmt
     *
     * @return string
     */
    private function mapOutputFormat($fmt)
    {
        switch ($fmt) {
            case 'jpg':
            case self::FORMAT_JPEG:
                return 'imagejpeg';
            case self::FORMAT_PNG:
                return 'imagepng';
            case self::FORMAT_GIF:
                return 'imagegif';
            case self::FORMAT_WBMP:
                return 'imagewbmp';
            // webp and xbm need save paths
            case self::FORMAT_WEBP:
                return 'imagewebp';
            case self::FORMAT_XBM:
                return 'imagexbm';
            default:
                return false;
        }
    }

    /**
     * isValidResource
     *
     * @param resource $gd
     *
     * @return boolean
     */
    private function isValidResource($gd)
    {
        return is_resource($gd) && 'gd' === get_resource_type($gd);
    }

}
