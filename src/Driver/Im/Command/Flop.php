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
 * @class Flip
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Flop extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return '-flop';
    }
}