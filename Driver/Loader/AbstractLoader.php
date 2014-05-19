<?php

/**
 * This File is part of the Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Loader;

/**
 * @class AbstractLoader
 * @package Loader
 * @version $Id$
 */
abstract class AbstractLoader implements LoaderInterface
{
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
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->clean();
    }

    /**
     * clean
     *
     * @access public
     * @return void
     */
    public function clean()
    {
        if (file_exists($this->file)) {
            @unlink($this->file);
        }

        $this->source = null;
    }

    /**
     * valid
     *
     * @param mixed $url
     * @access private
     * @return mixed
     */
    protected function validate($url)
    {
        if (@getimagesize($url)) {
            $this->source = $url;
            return $url;
        }

        return false;
    }
}
