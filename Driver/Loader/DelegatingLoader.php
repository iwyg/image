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
 * @class DelegatingLoader
 * @package Loader
 * @version $Id$
 */
class DelegatingLoader implements LoaderInterface
{
    /**
     * loader
     *
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * loaders
     *
     * @var array
     */
    protected $loaders;

    /**
     * @param array $loaders
     *
     * @access public
     */
    public function __construct(array $loaders = [])
    {
        $this->setLoaders($loaders);
    }

    /**
     * load
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function load($file)
    {
        $loader = $this->getLoader($file);
        $this->loader = null;

        return $loader->load($file);
    }

    /**
     * clean
     *
     *
     * @access public
     * @return void
     */
    public function clean()
    {
        if ($this->loader) {
            $this->loader->clean();
        }
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
        foreach ($this->loaders as $loader) {
            if ($loader->supports($file)) {
                $this->loader = $loader;
                return true;
            }
        }

        return false;
    }

    /**
     * clean
     *
     *
     * @access public
     * @return mixed
     */
    public function getSource()
    {
        if ($this->loader) {
            $this->loader->getSource();
        }
    }

    /**
     * addLoader
     *
     * @param LoaderInterface $loader
     *
     * @access public
     * @return void
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * setLoaders
     *
     * @param array $loaders
     *
     * @access public
     * @return void
     */
    public function setLoaders(array $loaders)
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * getLoader
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function getLoader($file)
    {
        //if ($this->loader) {
        //    return $this->loader;
        //}

        if (!$this->supports($file)) {
            throw new \InvalidArgumentException(sprintf('No suitable loader found for srouce %s', $file));
        }

        return $this->loader;
    }
}
