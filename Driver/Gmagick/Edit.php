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
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Driver\MagickHelper;
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
    public function resize(SizeInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
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
    public function canvas(SizeInterface $size, PointInterface $point, ColorInterface $color = null)
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
     * @param SizeInterface $size
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
        $map = $this->filterMap();

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
     * {@inheritdoc}
     */
    protected function getMagickFilters()
    {
        return [
            Gmagick::FILTER_UNDEFINED, Gmagick::FILTER_POINT,    Gmagick::FILTER_BOX,
            Gmagick::FILTER_TRIANGLE,  Gmagick::FILTER_HERMITE,  Gmagick::FILTER_HANNING,
            Gmagick::FILTER_HAMMING,   Gmagick::FILTER_BLACKMAN, Gmagick::FILTER_GAUSSIAN,
            Gmagick::FILTER_QUADRATIC, Gmagick::FILTER_CUBIC,    Gmagick::FILTER_CATROM,
            Gmagick::FILTER_MITCHELL,  Gmagick::FILTER_LANCZOS,  Gmagick::FILTER_BESSEL,
            Gmagick::FILTER_SINC
        ];
    }
}
