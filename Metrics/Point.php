<?php

/*
 * This File is part of the Thapp\Image\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Metrics;

/**
 * @class Point
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Point implements PointInterface
{
    private $x;
    private $y;

    public function __construct($x, $y)
    {
        $this->x = (int)$x;
        $this->y = (int)$y;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function isIn(BoxInterface $box)
    {
        return $this->x <= $box->getWidth() && $this->y <= $box->getHeight();
    }

    public function negate()
    {
        $x = $this->getX();
        $y = $this->getY();

        return new static(0 !== $x ? -$x : 0, 0 !== $y ? -$y : 0);
    }
}
