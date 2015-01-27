<?php

/*
 * This File is part of the Lucid\Image\Driver\Im\Expression package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Expression;

/**
 * @class Resize
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rotate implements ExpressionInterface
{
    private $deg;

    public function __construct($deg)
    {
        $this->deg = (float)$deg;
    }

    public function __toString()
    {
        return sprintf('-rotate %s', $this->deg);
    }
}
