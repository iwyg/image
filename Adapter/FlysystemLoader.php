<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Adapter;

use \League\Flysystem\FilesystemInterface;
use \Thapp\Image\Driver\Loader\AbstractLoader;

/**
 * @class FlysystemAdapter implements LoaderInterface
 * @see LoaderInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FlysystemLoader extends AbstractLoader
{
    /**
     * fs
     *
     * @var FilesystemInterface
     */
    protected $fs;

    /**
     * tmp
     *
     * @var string
     */
    protected $tmp;

    /**
     * @param FilesystemInterface $fs
     */
    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
        $this->tmp = sys_get_temp_dir();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->clean();
    }

    /**
     * load
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function load($url)
    {
        $tmp = tempnam($this->tmp, 'fly_');

        $this->fs->put($tmp, $this->fs->read($url));

        if (!$this->validate($tmp)) {
            $this->clean();
            throw new \InvalidArgumentException(sprintf('%s is not a valid image source', $url));
        }

        $this->source = $tmp;

        return $tmp;
    }

    /**
     * clean
     *
     * @access public
     * @return void
     */
    public function clean()
    {
        if (null === $this->source) {
            return;
        }

        $this->fs->delete($this->source);
        $this->source = null;
    }

    /**
     * supports
     *
     * @param mixed $url
     *
     * @access public
     * @return boolean
     */
    public function supports($url)
    {
        return $this->fs->has($url);
    }
}
