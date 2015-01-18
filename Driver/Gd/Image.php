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
    private $frames;

    public function __construct($resource)
    {
        $this->setResource($resource);
        $this->frames = new Frames($this);
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

        // TODO:
        // add background color
        $extent = $this->newFromGd($size);

        imagecopy($extent, $this->gd, 0, 0, $start->getX(), $start->getY(), $size->getWidth(), $size->getHeight());
        imagefill($extent, 0, 0, $this->getColorId($extent, $color));

        $this->swapGd($extent);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size)
    {
        $resized = $this->newFromGd($size);

        imagecopyresampled(
            $resized,
            $this->gd,
            0,
            0,
            0,
            0,
            $size->getWidth(),
            $size->getHeight(),
            $this->getWidth(),
            $this->getHeight()
        );

        $this->swapGd($resized);
    }

    /**
     * {@inheritdoc}
     */
    public function crop(BoxInterface $size, PointInterface $crop = null, ColorInterface $color = null)
    {
        if (null !== $crop) {
            $box = new Box($this->getWidth(), $this->getHeight());

            if (!$box->contains($size)) {
                $crop = $crop->negate();
            }
        }

        $this->extent($size, $crop, $color);
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
        $rotate = imagerotate($this->gd, -1 * $deg, $this->getColorId($this->gd, $color));

        $this->swapGd($rotate);

        $this->resize($size);
    }

    /**
     * {@inheritdoc}
     */
    public function get($format = null, array $options = [])
    {
        if (!$fn = $this->mapOutputFormat($format)) {
            throw new \RuntimeException();
        }

        return $this->generateOutPut($fn);
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
     * @return void
     */
    private function newFromGd(BoxInterface $size)
    {
        return imagecreatetruecolor($size->getWidth(), $size->getHeight());
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
        if (!is_resource($resource) || 'gd' !== get_resource_type($resource)) {
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
            return imagecolorallocatealpha($res, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha());
        }

        return imagecolorallocate($res, $color->getRed(), $color->getGreen(), $color->getBlue());
    }

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

    public static function gdCreateFromFile($file)
    {
        $gd = null;
        list($mime, ) = explode(';', finfo_file($info = finfo_open(FILEINFO_MIME), $file));
        finfo_close($info);

        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file);
            case 'image/png':
                return imagecreatefrompng($file);
            case 'image/gif':
                return imagecreatefromwgif($file);
            case 'image/vnd.wap.wbmp':
                return imagecreatefromwbmp($file);
            case 'image/webp':
                return imagecreatefromwebp($file);
            case 'image/x-xbitmap':
            case 'image/x-xbm':
                return imagecreatefromxbm($file);
            default:
                return false;
        }
    }
}
