<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Imagick;
use ImagickPixel;
use ImagickException;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Driver\MagickHelper;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Color\RgbInterface;
use Thapp\Image\Color\CmykInterface;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Parser;
use Thapp\Image\Exception\ImageException;
use Thapp\Image\Color\Palette\CmykPaletteInterface;

/**
 * @class Edit
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Edit extends AbstractEdit
{
    use MagickHelper,
        HelperTrait;

    /** @var array */
    private static $filterMap;

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
        //$alpha = Imagick::ALPHACHANNEL_ACTIVATE === $this->imagick()->getImageAlphaChannel() ? 0 : 1;

        $point = $this->getStartPoint($size, $start);

        //$this->imagick()->setBackgroundColor('none');
        //$this->imagick()->setImageBackgroundColor('none');

        if (null !== $color) {
            $pixel = $this->pixelFromColor($color);
            $this->imagick()->setImageBackgroundColor($pixel);
            //$this->imagick()->setBackgroundColor($pixel);

            //if ($this->isMatteImage($this->imagick())) {
            //    $overlay = new Imagick;
            //    $overlay->setBackgroundColor(new ImagickPixel('transparent'));
            //    $overlay->newImage($size->getWidth(), $size->getHeight(), $pixel);
            //    $this->imagick()->compositeImage($overlay, Imagick::COMPOSITE_DSTOVER, 0, 0);
            //} else {
            //    $this->imagick()->setImageBackgroundColor($pixel);
            //}
        } else {
            $this->imagick()->setImageBackgroundColor('none');
        }

        $this->imagick()->extentImage($size->getWidth(), $size->getHeight(), -$point->getX(), -$point->getY());
    }

    ///**
    // * {@inheritdoc}
    // */
    //public function canvas(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    //{
    //    try {
    //        $this->createCanvas($size, $this->getStartPoint($size, $start), $color, Imagick::COMPOSITE_OVER);
    //    } catch (ImagickException $e) {
    //        throw new ImageException('Cannot create canvas.', $e->getCode(), $e);
    //    }
    //}

    /**
     * {@inheritdoc}
     */
    public function resize(SizeInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        try {
            $this->imagick()->resizeImage($size->getWidth(), $size->getHeight(), $this->mapFilter($filter), 1);
        } catch (ImagickException $e) {
            throw new ImageException('Cannot resize image.', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        try {
            $this->imagick()->rotateImage(
                $px = $this->newPixel(
                    $color ?: $this->newColor([255, 255, 255])
                ),
                (float)$deg
            );
        } catch (ImagickException $e) {
            throw new ImageException('Cannot rotate image.', $e->getCode(), $e);
        }

        $px->clear();
        $px->destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function flip()
    {
        try {
            $this->imagick()->flipImage();
        } catch (ImagickException $e) {
            throw new ImageException('Cannot flip image.', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flop()
    {
        try {
            $this->imagick()->flopImage();
        } catch (\ImagickException $e) {
            throw new ImageException('Cannot flop image.', $e->getCode(), $e);
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

        // use gravity to get the start point if no point is given:
        $start = $this->getStartPoint($image->getSize(), $start)->negate();

        // use the first image in the set:
        $imagick = $image->getImagick();
        $index = $imagick->getIteratorIndex();
        $imagick->setFirstIterator();

        try {
            $this->doCopy($this->imagick(), $imagick, $start, Imagick::COMPOSITE_COPY);
        } catch (ImagickException $e) {
            throw new ImageException('Cannot paste image.', $e->getCode(), $e);
        }

        // reset the iterator index to the previous index:
        $imagick->setIteratorIndex($index);
    }

    /**
     * doCopy
     *
     * @param Imagick $canvas
     * @param SizeInterface $size
     * @param PointInterface $point
     * @param mixed $mode
     *
     * @return void
     */
    protected function doCopy(Imagick $canvas, Imagick $dest, PointInterface $point, $mode = Imagick::COMPOSITE_OVER)
    {
        if ($strip = ($canvas !== $this->imagick() && $dest === $this->imagick())) {
            $palette = $this->image->getPalette();
            //$canvas->setColorspace($dest->getColorspace());
            $canvas->setImageFormat($dest->getImageFormat());
            $dest->stripImage();
            $dest->setColorspace(Imagick::COLORSPACE_SRGB);
        }

        $canvas->compositeImage($dest, $dest->getImageCompose(), $point->getX(), $point->getY());

        if ($strip) {
            $canvas->setColorspace(Imagick::COLORSPACE_SRGB);
            //$canvas->setColorspace($dest->getColorspace());
            //$image = new Image($canvas, new Rgb);
            //$image->applyPalette($palette);
        }

        $this->image->swapImagick($canvas);
        //$this->imagick()->setImageAlphaChannel(Imagick::ALPHACHANNEL_DEACTIVATE);
    }

    /**
     * createCanvas
     *
     * @param SizeInterface $size
     * @param PointInterface $point
     * @param ColorInterface $color
     * @param mixed $mode
     *
     * @return void
     */
    protected function createCanvas(
        SizeInterface $size,
        PointInterface $point,
        ColorInterface $color = null,
        $mode = Imagick::COMPOSITE_OVER
    ) {
        $canvas = new Imagick();

        $color = null !== $color ? $this->pixelFromColor($color) : new ImagickPixel('srgba(255, 255, 255, 0)');
        $canvas->newImage($size->getWidth(), $size->getHeight(), $color);

        $this->doCopy($canvas, $this->imagick(), $point, $mode);
    }

    /**
     * imagick
     *
     * @return \Imagick
     */
    private function imagick()
    {
        return $this->image->getImagick();
    }

    /**
     * getFilter
     *
     * @param int $filter
     *
     * @return int
     */
    private function mapFilter($filter)
    {
        $map = $this->filterMap();

        if (!isset($map[$filter])) {
            return Imagick::FILTER_UNDEFINED;
        }

        return $map[$filter];
    }

    ///**
    // * mapMode
    // *
    // * @param string $mode
    // *
    // * @return int
    // */
    //private function mapMode($mode)
    //{
    //    if (array_key_exists($mode, $map = [
    //        self::COPY_DEFAULT => Imagick::COMPOSITE_COPY,
    //        self::COPY_OVER    => Imagick::COMPOSITE_OVER,
    //        self::COPY_OVERLAY => Imagick::COMPOSITE_OVERLAY
    //    ])
    //    ) {
    //        return $map[$mode];
    //    }

    //    return Imagick::COMPOSITE_DEFAULT;
    //}

    /**
     * newPixel
     *
     * @param ColorInterface $color
     *
     * @return ImagickPixel
     */
    private function newPixel(ColorInterface $color)
    {
        return $color ? $this->pixelFromColor($color) : new ImagickPixel(self::COLOR_NONE);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMagickFilters()
    {
        return [
            Imagick::FILTER_UNDEFINED, Imagick::FILTER_POINT,    Imagick::FILTER_BOX,
            Imagick::FILTER_TRIANGLE,  Imagick::FILTER_HERMITE,  Imagick::FILTER_HANNING,
            Imagick::FILTER_HAMMING,   Imagick::FILTER_BLACKMAN, Imagick::FILTER_GAUSSIAN,
            Imagick::FILTER_QUADRATIC, Imagick::FILTER_CUBIC,    Imagick::FILTER_CATROM,
            Imagick::FILTER_MITCHELL,  Imagick::FILTER_LANCZOS,  Imagick::FILTER_BESSEL,
            Imagick::FILTER_SINC
        ];
    }
}
