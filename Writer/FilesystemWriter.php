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
 * @class FilesystemWriter
 * @package Thapp\Image\Writer
 * @version $Id$
 */
class FilesystemWriter implements WriterInterface
{
    public function write($target, $data = null)
    {
        file_put_contents($target, $data);
    }
}
