<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Geometry\PointInterface;

/**
 * @class Extent
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Extent extends AbstractCommand
{
    private $size;
    private $point;

    public function __construct(SizeInterface $size, PointInterface $point)
    {
        $this->size  = $size;
        $this->point = $point;
    }

    public function asString()
    {
        $x = $this->point->getX();
        $y = $this->point->getY();
        if (0 === $x && 0 === $y) {
            $region = '';
        } else {
            $pX = 0 <= $x ? '+' : '';
            $pY = 0 <= $y ? '+' : '';
            $region = $pX.$x.$pY.$y;
        }

        return sprintf('-extent %sx%s%s', $this->size->getWidth(), $this->size->getHeight(), $region);
    }
}
