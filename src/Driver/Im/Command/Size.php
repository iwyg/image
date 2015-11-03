<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c)  <>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Geometry\SizeInterface;

/**
 * @class Size
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author  <>
 */
class Size extends AbstractCommand
{
    private $size;

    public function __construct(SizeInterface $size)
    {
        $this->size = $size;
    }

    public function asString()
    {
        return sprintf('-size %sx%s', $this->size->getWidth(), $this->size->getHeight());
    }
}
