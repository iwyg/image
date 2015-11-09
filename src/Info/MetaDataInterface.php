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

use ArrayAccess;

/**
 * @class MetaDataInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MetaDataInterface extends ArrayAccess
{
    /**
     * Get all attributes as map.
     *
     * @return array
     */
    public function all();

    /**
     * Sets an attribute.
     *
     * @param string $attr
     * @param string $value
     *
     * @return void
     */
    public function set($attr, $value);

    /**
     * Gets an attribute
     *
     * @param string $attr
     *
     * @return string
     */
    public function get($attr, $default = null);

    /**
     * Checks if an attribute exists.
     *
     * @param string $attr
     *
     * @return bool
     */
    public function has($attr);

    /**
     * Delete a attribute.
     *
     * @param string $attr
     *
     * @return void
     */
    public function delete($attr);
}
