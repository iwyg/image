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
     * Constructor.
     *
     * @param MetaDataReaderInterface $reader
     */
    public function __construct(MetaDataReaderInterface $reader = null)
    {
        $this->reader = $reader ?: new ImageReader;
    }

    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        $this->validateStream($resource);

        $meta = stream_get_meta_data($resource);

        try {
            // e.g. Gmagick::readImageFile may cause segfault error.
            // GD has no opton to read file streams.
            if (isset($meta['uri']) && stream_is_local($file = $meta['uri'])) {
                return $this->load($file);
            }
            return $this->create(stream_get_contents($resource));
        } catch (ImageException $e) {
            throw ImageException::read($e);
        }
    }

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
