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

/**
 * @class MetaDataReaderInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MetaDataReaderInterface
{
    /**
     * readFromFile
     *
     * @param string $file
     *
     * @return MetaDataInterface
     */
    public function readFromFile($file);

    /**
     * readFromBlob
     *
     * @param string $blob
     *
     * @return MetaDataInterface
     */
    public function readFromBlob($blob);

    /**
     * readFromResource
     *
     * @param mixed $resource
     *
     * @return MetaDataInterface
     */
    public function readFromStream($resource);
}
