<?php

/**
 * This File is part of the Thapp\JitImage package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Loader;

use Thapp\Image\Exception\SourceLoaderException;

/**
 * @class FilesystemLoader extends AbstractLoader
 * @see AbstractLoader
 *
 * @package Thapp\JitImage
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
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
     * @var string
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
            throw new SourceLoaderException(sprintf('source "%s" is not an image', $file));
        }

        return $source;
    }

    /**
     * supports
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function supports($file)
    {
        // prevent errors on unsupported stream wrappers:
        //if (null !== ($scheme = parse_url($file, PHP_URL_SCHEME)) && !in_array($scheme, ['vfs', 'file'])) {
        //    var_dump($scheme);
        //    return false;
        //}

        return is_file($file) && stream_is_local($file);
    }
}
