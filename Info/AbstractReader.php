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
 * @class AbstractReader
 *
 * @package Thapp\Image\Meta
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractReader implements MetaDataReaderInterface
{
    /**
     * getStreamUri
     *
     * @param mixed $resource
     *
     * @return void
     */
    protected function getStreamUri($resource)
    {
        $meta = stream_get_meta_data($resource);

        if (!isset($meta['uri']) && !stream_is_local($meta['uri'])) {
            return false;
        }

        return $meta['uri'];
    }

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

    abstract protected function getMappedKeys();
}
