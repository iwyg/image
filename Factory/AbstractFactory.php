<?php

/**
 * This File is part of the Thapp\Image\Factory package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Factory;

use \Thapp\Image\Image;
use \Thapp\Image\Writer\WriterInterface;
use \Thapp\Image\Writer\FilesystemWriter;
use \Thapp\Image\Driver\Loader\LoaderInterface;
use \Thapp\Image\Driver\Loader\RemoteLoader;
use \Thapp\Image\Driver\Loader\FilesystemLoader;
use \Thapp\Image\Driver\Loader\DelegatingLoader;
use \Thapp\Image\Cache\FilesystemCache;

/**
 * @class AbstractFactory
 * @package Thapp\Image\Factory
 * @version $Id$
 */
abstract class AbstractFactory
{
    /**
     * loader
     *
     * @var mixed
     */
    protected $loader;

    /**
     * writer
     *
     * @var mixed
     */
    protected $writer;

    /**
     * cache
     *
     * @var mixed
     */
    protected $cache;

    /**
     * processor
     *
     * @var mixed
     */
    protected $processor;

    /**
     * loaderInstantiator
     *
     * @var callable
     */
    protected static $loaderInstantiator;

    /**
     * writerInstantiator
     *
     * @var callable
     */
    protected static $writerInstantiator;

    /**
     * cacheInstantiator
     *
     * @var callable
     */
    protected static $cacheInstantiator;

    /**
     * __construct
     *
     * @param LoaderInterface $loader
     *
     * @access public
     * @return mixed
     */
    public function __construct(LoaderInterface $loader = null, WriterInterface $writer = null)
    {
        $this->loader = $loader?: $this->getLoaderInstance();
        $this->writer = $writer?: $this->getWriterInstance();
        $this->cache  = $writer?: $this->getCacheInstance();
    }

    /**
     * make
     *
     * @access public
     * @return mixed
     */
    public function make($class = null)
    {
        if (null !== $class) {
            return new $class($this->getProcessor(), $this->cache);
        }

        return new Image($this->getProcessor(), $this->cache);
    }

    /**
     * getProcessor
     *
     * @access protected
     * @return mixed
     */
    protected function getProcessor()
    {
        if (null === $this->processor) {
            $this->processor = $this->createProcessor();
        }

        return $this->processor;
    }

    /**
     * getLoaderInstance
     *
     * @access protected
     * @return mixed
     */
    protected function getLoaderInstance()
    {
        if ($this->loader) {
            return $this->loader;
        }

        if (is_callable($instantiator = static::$loaderInstantiator)) {
            return $this->loader =  call_user_func($instantiator);
        }

        return $this->loader = new DelegatingLoader([
            new FilesystemLoader,
            new RemoteLoader
        ]);
    }

    /**
     * getLoaderInstance
     *
     * @access protected
     * @return mixed
     */
    protected function getCacheInstance()
    {
        if ($this->cache) {
            return $this->cache;
        }

        if (is_callable($instantiator = static::$cacheInstantiator)) {
            return $this->cache =  call_user_func($instantiator);
        }

        return $this->cache = new FilesystemCache(getcwd() . DIRECTORY_SEPARATOR . 'cache');
    }

    /**
     * getLoaderInstance
     *
     * @access protected
     * @return mixed
     */
    protected function getWriterInstance()
    {
        if ($this->writer) {
            return $this->writer;
        }

        if (is_callable($instantiator = static::$writerInstantiator)) {
            return $this->writer = call_user_func($instantiator);
        }

        return $this->writer = new FilesystemWriter;
    }

    /**
     * setLoaderInstantiator
     *
     * @param callable $instantiator
     *
     * @access public
     * @return void
     */
    public static function setLoaderInstantiator(callable $instantiator)
    {
        static::$loaderInstantiator = $instantiator;
    }

    /**
     * setLoaderInstantiator
     *
     * @param callable $instantiator
     *
     * @access public
     * @return void
     */
    public static function setCacheInstantiator(callable $instantiator)
    {
        static::$cacheInstantiator = $instantiator;
    }

    /**
     * setWriterInstantiator
     *
     * @param callable $instantiator
     *
     * @access public
     * @return void
     */
    public static function setWriterInstantiator(callable $instantiator)
    {
        static::$writerInstantiator = $instantiator;
    }

    /**
     * createProcessor
     *
     *
     * @access protected
     * @abstract
     * @return \Thamm\Image\ProcessorInterface
     */
    abstract protected function createProcessor();
}
