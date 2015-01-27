<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Info\ImageReader;
use Thapp\Image\Info\MetaDataReaderInterface;
use Thapp\Image\Exception\ImageException;

/**
 * @class AbstractSource
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractSource implements SourceInterface
{
    protected $reader;

    /**
     * validateStream
     *
     * @param mixed $resource
     *
     * @return boolean
     */
    protected function validateStream($resource)
    {
        if (!is_resource($resource) || 'stream' !== get_resource_type($resource)) {
            throw ImageException::resource();
        }

        return true;
    }

    /**
     * Constructor.
     *
     * @param MetaDataReaderInterface $reader
     */
    public function __construct(MetaDataReaderInterface $reader = null)
    {
        $this->reader = $reader ?: new ImageReader;
    }

    /**
     * getReader
     *
     * @return MetaDataReaderInterface
     */
    protected function getReader()
    {
        if (null === $this->reader) {
            $this->reader = new ImageReader;
        }

        return $this->reader;
    }
}
