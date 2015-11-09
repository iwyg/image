<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Driver\Im\Command\CommandInterface;
use Thapp\Image\Driver\ImageInterface as BaseImageInterface;

/**
 * @interface ImageInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImageInterface extends BaseImageInterface
{
    /**
     * addCommand
     *
     * @param CommandInterface $command
     * @param SizeInterface $command
     *
     * @return void
     */
    public function addCommand(CommandInterface $command, SizeInterface $size = null);
}
