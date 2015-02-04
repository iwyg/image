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
use Thapp\Image\Exception\ImageException;

/**
 * @class Edit
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Edit extends AbstractEdit
{
    use MagickHelper;

    private static $filterMap;

    /**
     * Constructor.
     *
     * @param Image $image
     *
     * @return void
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
        $color = $color ?: $this->newColor([255, 255, 255, 0]);

        try {
            $this->createCanvas($size, $this->getStartPoint($size, $start), $color, Imagick::COMPOSITE_COPY);
            $this->imagick()->setImagePage(0, 0, 0, 0);
        } catch (ImagickException $e) {
            throw new ImageException('Cannot extent image.', $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canvas(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        try {
            $this->createCanvas($size, $this->getStartPoint($size, $start), $color, Imagick::COMPOSITE_OVER);
        } catch (ImagickException $e) {
            throw new ImageException('Cannot create canvas.', $e->getCode(), $e);
        }
    }

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
            $this->imagick()->rotateImage($px = $this->newPixel($color ?: $this->newColor([255, 255, 255])), (float)$deg);
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
        $canvas->compositeImage($dest, $mode, $point->getX(), $point->getY());

        if ($canvas !== $this->imagick() && $dest === $this->imagick()) {
            $canvas->setImageFormat($dest->getImageFormat());
            $this->image->swapImagick($canvas);
        }
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
    protected function createCanvas(SizeInterface $size, PointInterface $point, ColorInterface $color = null, $mode = Imagick::COMPOSITE_OVER)
    {
        $canvas = new Imagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? $color->getColorAsString() : self::COLOR_NONE);

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
        return new ImagickPixel($color ? $color->getColorAsString() : self::COLOR_NONE);
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
