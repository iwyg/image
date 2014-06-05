<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Loader;

/**
 * @abstract class AbstractLoader implements LoaderInterface
 * @see LoaderInterface
 * @abstract
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * getSource
     *
     * @access public
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Clean up un object removal
     *
     * @return void
     */
    public function __destruct()
    {
        $this->clean();
    }

    /**
     * Clone the loader instance.
     *
     * @return void
     */
    public function __clone()
    {
        $this->source = null;
    }

    /**
     * clean
     *
     * @return void
     */
    public function clean()
    {
        $this->source = null;
    }

    /**
     * valid
     *
     * @param string $url
     * @return boolean
     */
    protected function validate($url)
    {
        if (@getimagesize($url)) {
            return $this->source = $url;
        }

        return false;
    }
}
