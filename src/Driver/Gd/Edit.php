<?php

/*
 * This File is part of the Thapp\Image\Driver\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\RgbInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Exception\ImageException;

/**
 * @class Edit
 *
 * @package Thapp\Image\Driver\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Edit extends AbstractEdit
{
    use GdHelper;

    /**
     * Constructor.
     *
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function extent(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $start = $this->getStartPoint($size, $start);
        $color = $color ?: $this->image->getPalette()->getColor([255, 255, 255, 0]);

        try {
            $this->canvas($size, $start, $color);
        } catch (ImageException $e) {
            throw new ImageException('Cannot extent image.', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resize(SizeInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $resized = $this->image->newGd($size);

        $dw = $size->getWidth();
        $dh = $size->getHeight();
        $w  = $this->getWidth();
        $h  = $this->getHeight();

        if (true !== imagecopyresampled($resized, $this->gd(), 0, 0, 0, 0, $dw, $dh, $w, $h)) {
            throw new ImageException('Cannot resize image.');
        }

        $this->image->swapGd($resized);

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
        if (!$rotate = imagerotate($this->gd(), (float)(-1.0 * $deg), $this->getColorId($this->gd(), $color))) {
            throw new ImageException('Cannot rotate image.', $e->getCode(), $e);
        }

        $this->image->swapGd($rotate);

        $this->resize($size);
    }

    /**
     * {@inheritdoc}
     */
    protected function canvas(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $color = $color ?: $this->image->getPalette()->getColor([255, 255, 255, 0]);
        $extent = $this->image->newGd($size, $color);

        imagealphablending($extent, true);

        try {
            $this->doCopy($extent, $this->gd(), $this->getStartPoint($size, $start), 'canvas');
        } catch (ImageException $e) {
            throw new ImageException('Cannot create canvas.', $e->getCode(), $e);
        }

        imagealphablending($this->gd(), false);
    }

    /**
     * {@inheritdoc}
     */
    public function flip()
    {
        if (false === $this->doFlip()) {
            throw new ImageException('Cannot flip image.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flop()
    {
        if (false === $this->doFlop()) {
            throw new ImageException('Cannot flop image.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function paste(ImageInterface $image, PointInterface $start = null)
    {
        if (!$image instanceof Image) {
            throw new \LogicException('Can\'t copy image from different driver.');
        }

        $start = $this->getStartPoint($image->getSize(), $start)->negate();

        try {
            $this->doCopy($this->gd(), $image->getGd(), $start, 'paste');
        } catch (ImageException $e) {
            throw new ImageException('Cannot paste image.', $e->getCode(), $e);
        }
    }

    /**
     * doCopy
     *
     * @param mixed $gd
     * @param PointInterface $point
     * @param mixed $mode
     *
     * @return void
     */
    protected function doCopy($gd, $dest, PointInterface $start, $ops = 'paste')
    {
        if (false === imagecopy($gd, $dest, $start->getX(), $start->getY(), 0, 0, $this->getWidth(), $this->getHeight())
        ) {
            throw new ImageException('Cannot copy image');
        }

        if ($gd !== $this->gd()) {
            $this->image->swapGd($gd);
        }
    }

    /**
     * imagick
     *
     * @return \Imagick
     */
    private function gd()
    {
        return $this->image->getGd();
    }

    /**
     * doFlip
     *
     * @return boolean
     */
    private function doFlip()
    {
        if (function_exists('imageflip')) {
            return imageflip($this->gd(), IMG_FLIP_VERTICAL);
        }

        list ($gd, $dest, $width, $height, $i) = $this->flipFlopArgs();

        while ($i < $height && imagecopy($dest, $gd, 0, $i, 0, ($height - 1) - $i, $width, 1)) {
            $i++;
        }

        if ($i < $height) {
            imagedestroy($dest);

            return false;
        }

        $this->getImage()->swapGd($dest);

        return true;
    }

    /**
     * doFlop
     *
     * @return boolean
     */
    private function doFlop()
    {
        if (function_exists('imageflip')) {
            return imageflip($this->gd(), IMG_FLIP_HORIZONTAL);
        }

        list ($gd, $dest, $width, $height, $i) = $this->flipFlopArgs();

        $i = 0;
        while ($i < $width && imagecopy($dest, $gd, $i, 0, ($width - 1) - $i, 0, 1, $height)) {
            $i++;
        }

        if ($i < $width) {
            imagedestroy($dest);

            return false;
        }

        $this->getImage()->swapGd($dest);

        return true;
    }

    /**
     * flipFlopArgs
     *
     * @return array
     */
    private function flipFlopArgs()
    {
        $dest = $this->image->newGd($size = $this->getImage()->getSize());

        return [$this->gd(), $dest, $size->getWidth(), $size->getHeight(), 0];
    }
}
