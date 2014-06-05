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
use \Thapp\Image\Loader\AbstractLoader;

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
     * useTmp
     *
     * @var boolean
     */
    protected $useTmp;

    /**
     * @param FilesystemInterface $fs
     */
    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
        $this->tmp = sys_get_temp_dir();
        $this->useTmp = false;
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
        $file = $this->createTmpIfNotLocal($url);

        if (!$this->validate($file)) {
            $this->clean();
            throw new \InvalidArgumentException(sprintf('%s is not a valid image source', $url));
        }

        return $this->source = $file;
    }

    /**
     * createTmpIfNotLocal
     *
     * @param string $url
     *
     * @return string
     */
    private function createTmpIfNotLocal($url)
    {
        if (stream_is_local($url) && file_exists($url)) {
            return $url;
        }

        //$this->❨╯°□°❩╯︵┻━┻ = 'fuuuuu…';

        $this->useTmp = true;

        $file = tempnam($this->tmp, basename($url));
        file_put_contents($file, $this->fs->read($url));

        return $file;
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

    /**
     * clean
     *
     * @return void
     */
    public function clean()
    {
        if (null === $this->source) {
            $this->useTmp = false;

            return;
        }

        if (!$this->useTmp) {
            return;
        }

        if ($this->supports($this->source)) {
            $this->fs->delete($this->source);
        } else {
            @unlink($this->source);
        }

        $this->source = null;
        $this->useTmp = false;
    }
}
