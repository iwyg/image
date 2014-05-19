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

use Thapp\Image\Exception\SourceLoaderException;

/**
 * @class FileSystemLoader
 * @package Loader
 * @version $Id$
 */
class FilesystemLoader extends AbstractLoader
{
    /**
     * file
     *
     * @var string
     */
    protected $file;

    /**
     * src
     *
     * @var mixed
     */
    protected $source;

    /**
     * load
     *
     * @param mixed $file
     *
     * @access public
     * @return boolean
     */
    public function load($file)
    {
        if (!($source = $this->validate($file))) {
            throw new SourceLoaderException(sprintf('Invalid Source URL: %s', $file));
        }

        return $source;
    }

    /**
     * supports
     *
     * @param mixed $file
     *
     * @access public
     * @return boolean
     */
    public function supports($file)
    {
        return is_file($file) && stream_is_local($file);
    }
}
