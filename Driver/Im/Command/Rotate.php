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

/**
 * @class Rotate
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rotate extends AbstractCommand
{
    private $deg;

    public function __construct($deg)
    {
        $this->deg = $deg;
    }

    public function asString()
    {
        return sprintf('-rotate %s', $this->deg);
    }
}
