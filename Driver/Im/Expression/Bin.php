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
 * @class Bin
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Bin implements ExpressionInterface
{
    private $bin;

    public function __construct($bin)
    {
        $this->bin = $bin;
    }

    public function __toString()
    {
        return $this->bin;
    }
}
