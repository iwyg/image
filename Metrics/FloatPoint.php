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
 * @class FloatPoint
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FloatPoint extends Point
{
    private $x;
    private $y;

    public function __construct($x, $y)
    {
        $this->x = (float)$x;
        $this->y = (float)$y;
    }

    /**
     * {@inheritdoc}
     *
     * return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * {@inheritdoc}
     *
     * return float
     */
    public function getY()
    {
        return $this->y;
    }
}
