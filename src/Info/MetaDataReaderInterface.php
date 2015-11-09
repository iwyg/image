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
 * @interface MetaDataReaderInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MetaDataReaderInterface
{
    /**
     * Read from a given file path.
     *
     * @param string $file the file path
     *
     * @return \Thapp\Image\Info\MetaDataInterface
     */
    public function readFromFile($file);

    /**
     * Read from a given file blob.
     *
     * @param string $blob the file content
     *
     * @return \Thapp\Image\Info\MetaDataInterface
     */
    public function readFromBlob($blob);

    /**
     * Read from a given input stream.
     *
     * @param stream $resource stream resource
     *
     * @return \Thapp\Image\Info\MetaDataInterface
     */
    public function readFromStream($resource);
}
