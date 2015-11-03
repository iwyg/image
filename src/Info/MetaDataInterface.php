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

interface MetaDataInterface extends \ArrayAccess
{
    /**
     * Get all attributes as map.
     *
     * @return array
     */
    public function all();

    /**
     * set
     *
     * @param mixed $attr
     * @param mixed $value
     *
     * @return void
     */
    public function set($attr, $value);

    /**
     * Gets an attribute
     *
     * @param string $attr
     *
     * @return mixed
     */
    public function get($attr, $default = null);

    /**
     * has
     *
     * @param string $attr
     *
     * @return boolean
     */
    public function has($attr);

    /**
     * delete
     *
     * @param mixed $attr
     *
     * @return void
     */
    public function delete($attr);
}
