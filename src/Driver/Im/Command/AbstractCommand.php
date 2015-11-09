<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

/**
 * @class AbstractCommand
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractCommand implements CommandInterface
{
    /**
     * Return command es string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->asString();
    }

    /**
     * @see as string
     *
     * @return string
     */
    abstract public function asString();
}
