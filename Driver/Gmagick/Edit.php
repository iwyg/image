<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gmagick;

use Gmagick;
use GmagickPixel;
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
 * @package Thapp\Image\Driver\Gmagick
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

        if ($this->getSize()->contains($size)) {
            $color = $this->image->getPalette()->getColor([255, 255, 255, 0]);
        } else {
            $color = $color ?: $this->image->getPalette()->getColor([255, 255, 255, 0]);
        }

        $this->canvas($size, $start, $color);

        $this->gmagick()->setImagePage(0, 0, 0, 0);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->gmagick()->resizeImage($size->getWidth(), $size->getHeight(), $this->mapFilter($filter), 1);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        $this->gmagick()->rotateImage($px = $this->newPixel($color), (float)$deg);

        $px->clear();
        $px->destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function canvas(BoxInterface $size, PointInterface $point, ColorInterface $color = null)
    {
        $canvas = new gmagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? $color->getColorAsString() : self::COLOR_NONE);

        $this->doCopy($canvas, $point, Gmagick::COMPOSITE_OVER);
    }

    /**
     * {@inheritdoc}
     */
    public function paste(ImageInterface $image, PointInterface $start = null)
    {
        if (!$image instanceof Image) {
            throw new \LogicException('Can\'t copy image from different driver.');
        }

        $this->doCopy($image->getGmagick(), $start ?: new Point(0, 0), Gmagick::COMPOSITE_DEFAULT);
    }

    /**
     * doCopy
     *
     * @param Gmagick $canvas
     * @param BoxInterface $size
     * @param PointInterface $point
     * @param mixed $mode
     *
     * @return void
     */
    protected function doCopy(Gmagick $canvas, PointInterface $point, $mode = Gmagick::COMPOSITE_OVER)
    {
        $canvas->compositeImage($this->gmagick(), $mode, $point->getX(), $point->getY());
        $canvas->setImageFormat($this->gmagick()->getImageFormat());

        $this->image->swapGmagick($canvas);
    }

    /**
     * imagick
     *
     * @return \Gmagick
     */
    private function gmagick()
    {
        return $this->image->getGmagick();
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
            return Gmagick::FILTER_UNDEFINED;
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
            self::COPY_DEFAULT => Gmagick::COMPOSITE_COPY,
            self::COPY_OVER => Gmagick::COMPOSITE_OVER,
            self::COPY_OVERLAY => Gmagick::COMPOSITE_OVERLAY
        ])
        ) {
            return $map[$mode];
        }

        return Gmagick::COMPOSITE_DEFAULT;
    }

    /**
     * newPixel
     *
     * @param ColorInterface $color
     *
     * @return GmagickPixel
     */
    private function newPixel(ColorInterface $color)
    {
        return new GmagickPixel($color ? $color->getColor() : self::COLOR_NONE);
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
                ImageInterface::FILTER_UNDEFINED => Gmagick::FILTER_UNDEFINED,
                ImageInterface::FILTER_POINT     => Gmagick::FILTER_POINT,
                ImageInterface::FILTER_BOX       => Gmagick::FILTER_BOX,
                ImageInterface::FILTER_TRIANGLE  => Gmagick::FILTER_TRIANGLE,
                ImageInterface::FILTER_HERMITE   => Gmagick::FILTER_HERMITE,
                ImageInterface::FILTER_HANNING   => Gmagick::FILTER_HANNING,
                ImageInterface::FILTER_HAMMING   => Gmagick::FILTER_HAMMING,
                ImageInterface::FILTER_BLACKMAN  => Gmagick::FILTER_BLACKMAN,
                ImageInterface::FILTER_GAUSSIAN  => Gmagick::FILTER_GAUSSIAN,
                ImageInterface::FILTER_QUADRATIC => Gmagick::FILTER_QUADRATIC,
                ImageInterface::FILTER_CUBIC     => Gmagick::FILTER_CUBIC,
                ImageInterface::FILTER_CATROM    => Gmagick::FILTER_CATROM,
                ImageInterface::FILTER_MITCHELL  => Gmagick::FILTER_MITCHELL,
                ImageInterface::FILTER_LANCZOS   => Gmagick::FILTER_LANCZOS,
                ImageInterface::FILTER_BESSEL    => Gmagick::FILTER_BESSEL,
                ImageInterface::FILTER_SINC      => Gmagick::FILTER_SINC
            ];
        }

        return static::$filterMap;
    }
}
