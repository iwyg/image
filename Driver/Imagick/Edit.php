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
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Color\ColorInterface;

/**
 * @class Edit
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Edit extends AbstractEdit
{
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
    public function extent(BoxInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $start = $this->getStartPoint($size, $start);

        //if ($this->getSize()->contains($size)) {
        //    $color = $this->newColor([255, 255, 255, 0]);
        //} else {
        //    $color = $color ?: $this->newColor([255, 255, 255, 0]);
        //}
        //
        $color = $color ?: $this->newColor([255, 255, 255, 0]);

        //$this->canvas($size, $start, $color);

        $canvas = new Imagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), $color ? $color->getColorAsString() : 'transparent');
        $this->doCopy($canvas, $this->imagick(), $start, Imagick::COMPOSITE_COPY);

        $this->imagick()->setImagePage(0, 0, 0, 0);
        //$canvas->compositeImage($this->imagick(), Imagick::COMPOSITE_COPY, $start->getX(), $start->getY());
        //$canvas->setImageFormat($this->image->getFormat());

        //$this->image->swapImagick($canvas);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->imagick()->resizeImage($size->getWidth(), $size->getHeight(), $this->mapFilter($filter), 1);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        $this->imagick()->rotateImage($px = $this->newPixel($color ?: $this->newColor([255, 255, 255])), (float)$deg);

        $px->clear();
        $px->destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function canvas(BoxInterface $size, PointInterface $point, ColorInterface $color = null)
    {
        $canvas = new Imagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? $color->getColorAsString() : self::COLOR_NONE);

        $this->doCopy($canvas, $this->imagick(), $point, Imagick::COMPOSITE_OVER);
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

        $this->doCopy($this->imagick(), $imagick, $start, Imagick::COMPOSITE_COPY);

        // reset the iterator index to the previous index:
        $imagick->setIteratorIndex($index);
    }

    /**
     * doCopy
     *
     * @param Imagick $canvas
     * @param BoxInterface $size
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
        $map = static::filterMap();

        if (!isset($map[$filter])) {
            return Imagick::FILTER_UNDEFINED;
        }

        return $map[$filter];
    }

    /**
     * mapMode
     *
     * @param string $mode
     *
     * @return int
     */
    private function mapMode($mode)
    {
        if (array_key_exists($mode, $map = [
            self::COPY_DEFAULT => Imagick::COMPOSITE_COPY,
            self::COPY_OVER => Imagick::COMPOSITE_OVER,
            self::COPY_OVERLAY => Imagick::COMPOSITE_OVERLAY
        ])
        ) {
            return $map[$mode];
        }

        return Imagick::COMPOSITE_DEFAULT;
    }

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
     * filterMap
     *
     * @return array
     */
    private static function &filterMap()
    {
        if (null === static::$filterMap) {
            static::$filterMap = [
                ImageInterface::FILTER_UNDEFINED => Imagick::FILTER_UNDEFINED,
                ImageInterface::FILTER_POINT     => Imagick::FILTER_POINT,
                ImageInterface::FILTER_BOX       => Imagick::FILTER_BOX,
                ImageInterface::FILTER_TRIANGLE  => Imagick::FILTER_TRIANGLE,
                ImageInterface::FILTER_HERMITE   => Imagick::FILTER_HERMITE,
                ImageInterface::FILTER_HANNING   => Imagick::FILTER_HANNING,
                ImageInterface::FILTER_HAMMING   => Imagick::FILTER_HAMMING,
                ImageInterface::FILTER_BLACKMAN  => Imagick::FILTER_BLACKMAN,
                ImageInterface::FILTER_GAUSSIAN  => Imagick::FILTER_GAUSSIAN,
                ImageInterface::FILTER_QUADRATIC => Imagick::FILTER_QUADRATIC,
                ImageInterface::FILTER_CUBIC     => Imagick::FILTER_CUBIC,
                ImageInterface::FILTER_CATROM    => Imagick::FILTER_CATROM,
                ImageInterface::FILTER_MITCHELL  => Imagick::FILTER_MITCHELL,
                ImageInterface::FILTER_LANCZOS   => Imagick::FILTER_LANCZOS,
                ImageInterface::FILTER_BESSEL    => Imagick::FILTER_BESSEL,
                ImageInterface::FILTER_SINC      => Imagick::FILTER_SINC
            ];
        }

        return static::$filterMap;
    }
}
