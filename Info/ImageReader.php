<?php

/*
 * This File is part of the Thapp\Image\Meta package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Info;

/**
 * @class ImageReader
 *
 * @package Thapp\Image\Meta
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageReader extends AbstractReader implements ImagesizeReaderInterface
{
    static $mappedKeys = [
        0 => 'width',
        1 => 'height',
        'mime' => 'MimeType',
    ];

    /**
     * readFromFile
     *
     * @param string $file
     *
     * @return MetaDataInterface
     */
    public function readFromFile($file)
    {
        if (false === $size = @getimagesize($file)) {
            throw new \RuntimeException('Cannot read file.');
        }

        return new MetaData($this->map($size));
    }
    /**
     * readFromBlob
     *
     * @param string $blob
     *
     * @return MetaDataInterface
     */
    public function readFromBlob($blob)
    {
        if (false === ($size = @getimagesizefromstring($blob))) {
            throw new \RuntimeException('Cannot read info from string.');
        }

        return new MetaData($this->map($size));
    }

    protected function getMappedKeys()
    {
        return static::$mappedKeys;
    }
}
