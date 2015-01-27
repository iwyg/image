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
 * @class MetaData
 * @see MetaDataInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MetaData implements MetaDataInterface
{
    private $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function set($attr, $value)
    {
        return $this->attributes[$attr] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attr)
    {
        return isset($this->attributes[$attr]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($attr, $default = null)
    {
        return $this->has($attr) ? $this->attributes[$attr] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($attr)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
}
