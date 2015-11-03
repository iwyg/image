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

use Thapp\Image\Geometry\PointInterface;

/**
 * @class ColorAt
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ColorAt extends AbstractCommand
{
    public function __construct(PointInterface $point)
    {
        $this->point = $point;
    }

    public function asString()
    {
        return sprintf('-format "%%[pixel:p{%s, %s}]" info:', $this->point->getX(), $this->point->getY());
    }
}
