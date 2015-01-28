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
        'meta' => 'MimeType',
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
        if (!$size = getimagesize($file)) {
            $size = [];
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
        if (!$size = getimagesizefromstring($blob)) {
            $size = [];
        }

        return new MetaData($this->map($size));
    }

    /**
     * readFromResource
     *
     * @param mixed $resource
     *
     * @return MetaDataInterface
     */
    public function readFromStream($resource)
    {
        if ($path = $this->getStreamUri($resource)) {
            return $this->readFromFile($path);
        }

        $pos = ftell($resource);
        rewind($resource);

        $meta = $this->readFromBlob(stream_get_contents($resource));
        fseek($resource, $pos);

        return $meta;
    }

    protected function getMappedKeys()
    {
        return static::$mappedKeys;
    }
}
