<?php

/**
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

/**
 * @class ImBinLocator
 * @package Thapp\Image\Driver
 * @version $Id$
 */
class ImBinLocator implements BinLocatorInterface
{
    /**
     * path
     *
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->path = $path;
    }

    /**
     * setConverterPath
     *
     * @param mixed $path
     * @access public
     * @return mixed
     */
    public function setConverterPath($path)
    {
        return $this->path = $path;
    }

    /**
     * getConverterPath
     *
     * @param mixed $param
     * @access public
     * @return mixed
     */
    public function getConverterPath()
    {
        if (null !== $this->path) {
            return $this->path;
        }

        foreach ($this->getDefaultPaths() as $path) {
            if (is_file($path)) {
                $this->path = $path;
                return $path;
            }
        }

        throw new \RuntimeException('No suitible binary found');
    }

    protected function getDefaultPaths()
    {
        return [
            '/bin/convert',
            '/usr/bin/convert',
            '/usr/local/bin/convert'
        ];
    }
}
