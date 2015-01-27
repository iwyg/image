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

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\RgbInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\ImageInterface;

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
    public function extent(BoxInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $start = $this->getStartPoint($size, $start);
        $color = $color ?: $this->image->getPalette()->getColor([255, 255, 255, 0]);

        $this->canvas($size, $start, $color);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $resized = $this->image->newGd($size);

        $dw = $size->getWidth();
        $dh = $size->getHeight();
        $w  = $this->getWidth();
        $h  = $this->getHeight();

        if (true !== imagecopyresampled($resized, $this->gd(), 0, 0, 0, 0, $dw, $dh, $w, $h)) {
            throw new \RuntimeException('Resizing of image failed.');
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
        $rotate = imagerotate($this->gd(), (float)(-1.0 * $deg), $this->getColorId($this->gd(), $color));

        $this->image->swapGd($rotate);

        $this->resize($size);
    }

    /**
     * {@inheritdoc}
     */
    public function canvas(BoxInterface $size, PointInterface $point, ColorInterface $color = null)
    {
        $color = $color ?: $this->image->getPalette()->getColor([255, 255, 255]);
        $extent = $this->image->newGd($size, $color);

        $this->doCopy($extent, $this->gd(), $point, 'canvas');
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

        $this->doCopy($this->gd(), $image->getGd(), $start, 'paste');
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
        try {
            imagecopy($gd, $dest, $start->getX(), $start->getY(), 0, 0, $this->getWidth(), $this->getHeight());
        }  catch (\Exception $e) {
            var_dump($ops);
        }

        if ($gd !== $this->gd()) {
            //var_dump($ops . ' swapp GD.');
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
}
