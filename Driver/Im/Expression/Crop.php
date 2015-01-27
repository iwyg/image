<?php

/*
 * This File is part of the Lucid\Image\Driver\Im\Expression package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Image\Driver\Im\Expression;

use Lucid\Image\Metrics\BoxInterface;
use Lucid\Image\Metrics\PointInterface;

/**
 * @class Crop
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Crop implements ExpressionInterface
{
    private $box;
    private $point;

    public function __construct(BoxInterface $box, PointInterface $point)
    {
        $this->box = $box;
        $this->point = $point;
    }

    public function __toString()
    {
        $x = $this->point->getX();
        $y = $this->point->getY();
        $sigX = ($x > 0 ? '+' : '-').(string)$x;
        $sigY = ($y > 0 ? '+' : '-').(string)$y;

        return sprintf('-crop %sx%s%s%s', $this->box->getWidth(), $this->box->getHeight(), $sigX, $sigY);
    }
}
