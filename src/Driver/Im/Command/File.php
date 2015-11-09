<?php

/*
 * This File is part of the Thapp\Image\Driver package
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
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class File extends AbstractCommand
{
    /** @var string */
    private $file;

    /** @var string */
    private $format;

    /**
     * Constructor.
     *
     * @param string $file
     * @param string $format
     *
     * @return void
     */
    public function __construct($file, $format = null)
    {
        $this->file = $file;
        $this->format = $format;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the file format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        $prefix = null === $this->format ? '' : strtoupper($this->format) . ':';

        return sprintf('%s%s', $prefix, $this->file);
    }
}
