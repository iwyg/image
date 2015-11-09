<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Info;

use RuntimeException;

/**
 * @class ImageReader
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageReader extends AbstractReader implements ImagesizeReaderInterface
{
    /** @var array */
    private static $mappedKeys = [
        0      => 'width',
        1      => 'height',
        'mime' => 'MimeType',
    ];

    /**
     * {@inheritdoc}
     */
    public function readFromFile($file)
    {
        if (false === $size = @getimagesize($file)) {
            throw new RuntimeException('Cannot read file.');
        }

        return new MetaData($this->map($size));
    }

    /**
     * {@inheritdoc}
     */
    public function readFromBlob($blob)
    {
        if (false === ($size = @getimagesizefromstring($blob))) {
            throw new RuntimeException('Cannot read info from string.');
        }

        return new MetaData($this->map($size));
    }

    /**
     * {@inheritdoc}
     */
    protected function getMappedKeys()
    {
        return static::$mappedKeys;
    }
}
