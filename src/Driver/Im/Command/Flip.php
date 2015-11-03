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
 * @class Flip
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Flip extends AbstractCommand
{
    public function asString()
    {
        return '-flip';
    }
}
