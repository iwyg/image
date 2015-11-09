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
 * @class AbstractReader
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractReader implements MetaDataReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function readFromStream($resource)
    {
        if ($path = $this->getStreamUri($resource)) {
            return $this->readFromFile($path);
        }

        return $this->readFromBlob(stream_get_contents($resource));
    }

    /**
     * Get the stream url.
     *
     * @param resource $resource
     *
     * @return string
     */
    protected function getStreamUri($resource)
    {
        $meta = stream_get_meta_data($resource);

        if (!isset($meta['uri']) || !stream_is_local($meta['uri'])) {
            return false;
        }

        return $meta['uri'];
    }

    /**
     * map
     *
     * @param array $data
     *
     * @return array
     */
    protected function map(array $data)
    {
        foreach ($this->getMappedKeys() as $key => $value) {
            if (isset($data[$key])) {
                $data[$value] = $data[$key];
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Get the keys that should be reflected in the meta data array.
     *
     * @return array
     */
    abstract protected function getMappedKeys();
}
