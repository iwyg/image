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
        $color = $color ?: $this->newColor([255, 255, 255, 0]);

        $this->createCanvas($size, $this->getStartPoint($size, $start), $color, Gmagick::COMPOSITE_COPY);
    }

    /**
     * {@inheritdoc}
     */
    public function canvas(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $this->createCanvas($size, $this->getStartPoint($size, $start), $color, Gmagick::COMPOSITE_OVER);
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
        $gmagick = $image->getGmagick();
        $index = $gmagick->getImageIndex();
        $gmagick->setImageIndex(0);

        $this->doCopy($this->gmagick(), $gmagick, $start, Gmagick::COMPOSITE_COPY);

        // reset the iterator index to the previous index:
        $gmagick->setImageIndex($index);
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
    protected function doCopy(Gmagick $canvas, Gmagick $dest, PointInterface $point, $mode = Gmagick::COMPOSITE_OVER)
    {
        $canvas->compositeImage($dest, $mode, $point->getX(), $point->getY());
        $canvas->setImageFormat($dest->getImageFormat());

        if ($canvas !== $this->gmagick() && $dest === $this->gmagick()) {
            $canvas->setImageFormat($dest->getImageFormat());
            $this->image->swapGmagick($canvas);
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
    protected function createCanvas(SizeInterface $size, PointInterface $point, ColorInterface $color = null, $mode = Gmagick::COMPOSITE_OVER)
    {
        $canvas = new Gmagick();
        $canvas->newImage($size->getWidth(), $size->getHeight(), null !== $color ? $color->getColorAsString() : self::COLOR_NONE);

        $this->doCopy($canvas, $this->gmagick(), $point, $mode);
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
    private function newPixel(ColorInterface $color = null)
    {
        return new GmagickPixel($color ? $color->getColorAsString() : self::COLOR_NONE);
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
