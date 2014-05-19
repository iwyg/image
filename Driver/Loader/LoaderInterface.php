<?php

/**
 * This File is part of the Thapp\JitImage package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Loader;

/**
 * Interface SourceLoaderInterface
 *
 *
 * @package Thapp\JitImage
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface LoaderInterface
{
    /**
     * load
     *
     * @param mixed $url
     *
     * @access public
     * @return mixed
     */
    public function load($url);

    /**
     * clean
     *
     * @access public
     * @return void
     */
    public function clean();

    /**
     * getSource
     *
     * @access public
     * @return string
     */
    public function getSource();

    /**
     * supports
     *
     * @param mixed $url
     *
     * @access public
     * @return boolean
     */
    public function supports($url);
}
