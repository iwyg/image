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
 * @class Strip
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Strip extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return '-strip';
    }
}
