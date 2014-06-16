<?php

/**
 * This File is part of the Thapp\Image\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Writer;

/**
 * @class FilesystemWriter implements WriterInterface
 * @see WriterInterface
 *
 * @package Thapp\Image\Writer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FilesystemWriter implements WriterInterface
{
    public function write($target, $data = null)
    {
        if (!is_dir($dir = dirname($target))) {
            mkdir($dir, 0755, true);
        }

        touch($target);
        file_put_contents($target, $data);
    }
}
