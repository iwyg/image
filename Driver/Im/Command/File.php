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
 * @class File
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class File extends AbstractCommand
{
    private $file;
    private $format;
    public function __construct($file, $format = null)
    {
        $this->file = $file;
        $this->format = $format;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function asString()
    {
        $prefix = null === $this->format ? '' : strtoupper($this->format) . ':';

        return sprintf('%s%s', $prefix, $this->file);
    }
}
