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
 * Interface SourceLoaderInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface LoaderInterface
{
    /**
     * Loads an image url.
     *
     * @param string $url
     *
     * @return mixed
     */
    public function load($url);

    /**
     * Clean the loader instance.
     *
     * @return void
     */
    public function clean();

    /**
     * Get the loaded source as string.
     *
     * @return string
     */
    public function getSource();

    /**
     * Checks if this loader supports the image url.
     *
     * @param string $url
     *
     * @return boolean
     */
    public function supports($url);

    /**
     * Clones a loader instance.
     *
     * @return void
     */
    public function __clone();
}
