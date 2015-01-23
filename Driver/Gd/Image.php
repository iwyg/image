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

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Color\Rgb;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\GdFilter;

/**
 * @class Image
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    private $gd;
    private $sourceFormat;

    /**
     * Constructor.
     *
     * @param resource $resource GD Image resource
     */
    public function __construct($resource)
    {
        $this->setResource($resource);
        $this->frames = new Frames($this);
    }

    /**
     * Destroy GD resource;
     *
     * @return void
     */
    public function __destruct()
    {
        $this->destroy();
    }

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
    public function frames()
    {
        return $this->frames;
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null)
    {
        $resource = imagecreatetrucolor($this->getWidth(), $this->getHeight());

        return new static($resource);
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
        if (is_resource($this->gd)) {
            imagedestroy($this->gd);
        }

        $this->setResource($resource);
    }

    public function setSourceFormat($format)
    {
        $this->sourceFormat = $this->mapFormat($format);
    }

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
    public function extent(BoxInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $start = $this->getStartPoint($size, $start);

        $color = $color ?: new Rgb(255, 255, 255, 0);
        $extent = $this->newFromGd($size, $color);
        imagecopy($extent, $this->gd, $start->getX(), $start->getY(), 0, 0, $this->getWidth(), $this->getHeight());
        $this->swapGd($extent);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = self::FILTER_UNDEFINED)
    {
        $resized = $this->newFromGd($size);

        $dw = $size->getWidth();
        $dh = $size->getHeight();
        $w  = $this->getWidth();
        $h  = $this->getHeight();

        if (true !== imagecopyresampled($resized, $this->gd, 0, 0, 0, 0, $dw, $dh, $w, $h)) {
            throw new \RuntimeException('Resizing of image failed.');
        }

        $this->swapGd($resized);

        imagealphablending($resized, false);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        if (0.0 === (float)($deg % 360)) {
            return;
        }

        $size = $this->getSize()->rotate($deg);
        $rotate = imagerotate($this->gd, (float)(-1.0 * $deg), $this->getColorId($this->gd, $color));

        $this->swapGd($rotate);

        $this->resize($size);
    }

    /**
     * {@inheritdoc}
     */
    public function get($format = null, array $options = [])
    {
        if (null === $format) {
            $format = ($fmt = $this->getOption($options, 'format', false)) ? $fmt : $this->getFormat();
        }

        if (!$fn = $this->mapOutputFormat($format)) {
            throw new \RuntimeException(sprintf('Unsupported format "%s".', $format));
        }

        if (in_array($format, $fmts = ['png', 'gif'])) {
            /*imagealphablending($this->gd, false);*/
            /*imagesavealpha($this->gd, true);*/
        } elseif (in_array($this->sourceFormat, $fmts)) {
            // copy image to white background:
            /*$canvas = $this->newFromGd($this->getSize());*/
            /*imagefill($canvas, 0, 0, $this->getColorId($canvas, new Rgb(255, 255, 255)));*/
            /*imagecopy($canvas, $this->gd, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());*/
            /*$this->swapGd($canvas);*/
        }

        return $this->generateOutPut($fn);
    }

    /**
     * hasAlpha
     *
     * @param mixed $gd
     *
     * @return void
     */
    private function hasAlpha($gd = null)
    {
        $channels = $this->channels($gd ?: $this->gd);

        return isset($channels['alpha']);
    }

    /**
     * channels
     *
     * @param mixed $gd
     *
     * @return void
     */
    private function channels($gd)
    {
        $rgb = imagecolorat($gd, 0, 0);

        return imagecolorsforindex($gd, $rgb);
    }

    /**
     * generateOutPut
     *
     * @param string $fn
     *
     * @return string
     */
    private function generateOutPut($fn)
    {
        ob_start();
        call_user_func($fn, $this->gd);

        return ob_get_clean();
    }

    /**
     * newFromGd
     *
     * @param BoxInterface $size
     *
     * @return resource
     */
    private function newFromGd(BoxInterface $size, ColorInterface $color = null)
    {
        $gd = imagecreatetruecolor($w = $size->getWidth(), $h = $size->getHeight());
        $color = $color ?: new Rgb(255, 255, 255);

        if (!(bool)imagealphablending($gd, false) || !(bool)imagesavealpha($gd, true)) {
            throw new \RuntimeException('Cannot create image.');
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

        $this->gd = $resource;
    }

    /**
     * getColorId
     *
     * @param mixed $res
     * @param mixed $color
     *
     * @return void
     */
    private function getColorId($res, ColorInterface $color = null)
    {
        if (null === $color) {
            return imagecolorallocate($res, 255, 255, 255);
        }

        if (null !== $color->getAlpha()) {

            if (0.0 === $a = $color->getAlpha()) {
                $alpha = 127;
            } else {
                $alpha = (int)round(abs(($a * 127) - 127));
            }

            return imagecolorallocatealpha($res, $color->getRed(), $color->getGreen(), $color->getBlue(), $alpha);
        }

        return imagecolorallocate($res, $color->getRed(), $color->getGreen(), $color->getBlue());
    }

    /**
     * mapOutputFormat
     *
     * @param mixed $fmt
     *
     * @return void
     */
    private function mapOutputFormat($fmt)
    {
        switch ($fmt) {
            case 'jpg':
            case 'jpeg':
                return 'imagejpeg';
            case 'png':
                return 'imagepng';
            case 'gif':
                return 'imagegif';
            case 'wbmp':
                return 'imagewbmp';
            case 'webp':
                return 'imagewebp';
            case 'xbm':
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
