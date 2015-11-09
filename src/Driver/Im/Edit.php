<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use InvalidArgumentException;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\AbstractEdit;
use Thapp\Image\Driver\Im\Command\Flip;
use Thapp\Image\Driver\Im\Command\Flop;
use Thapp\Image\Driver\Im\Command\Filter;
use Thapp\Image\Driver\Im\Command\Extent;
use Thapp\Image\Driver\Im\Command\Resize;
use Thapp\Image\Driver\Im\Command\Rotate;
use Thapp\Image\Driver\Im\Command\Background;
use Thapp\Image\Driver\MagickHelper;
use Thapp\Image\Driver\ImageInterface as BaseImageInterface;

/**
 * @class Edit
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Edit extends AbstractEdit
{
    use MagickHelper;

    /** @var array */
    private static $filterMap;

    /**
     * Constructor.
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function extent(SizeInterface $size, PointInterface $start = null, ColorInterface $color = null)
    {
        $this->image->addCommand(new Background($color));
        $this->image->addCommand(new Extent($size, $this->getStartPoint($size, $start)->negate()), $size);
    }

    /**
     * {@inheritdoc}
     */
    public function resize(SizeInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $this->image->addCommand(new Resize($size, $this->mapFilter($filter)), $size);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg, ColorInterface $color = null)
    {
        if (0 === $deg % 360) {
            return;
        }

        $this->image->addCommand(new Rotate($deg), $size = $this->image->getSize()->rotate($deg));
        $this->image->addCommand(new Resize($size, new Filter('Qubic')), $size);
    }

    /**
     * {@inheritdoc}
     */
    public function flip()
    {
        $this->image->addCommand(new Flip);
    }

    /**
     * {@inheritdoc}
     */
    public function flop()
    {
        $this->image->addCommand(new Flop);
    }

    /**
     * {@inheritdoc}
     */
    public function paste(BaseImageInterface $image, PointInterface $start = null)
    {
        if (false === $image instanceof ImageInterface) {
            throw new InvalidArgumentException('Can\'t copy image from different driver.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getMagickFilters()
    {
        return [
            null,        'Point',    'Box',
            'Triangle',  'Hermite',  'Hanning',
            'Hamming',   'Blackman', 'Gaussian',
            'Quadratic', 'Cubic',    'Catrom',
            'Mitchell',  'Lanczos',  'Bessel',
            'Sinc'
        ];
    }

    /**
     * mapFilter
     *
     * @param int $filter
     *
     * @return string
     */
    private function mapFilter($filter)
    {
        if (!array_key_exists($filter, $map = $this->filterMap())) {
            return null;
        }

        return $map[$filter];
    }
}
