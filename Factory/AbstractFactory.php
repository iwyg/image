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
    }

    /**
     * make
     *
     * @access public
     * @return mixed
     */
    public function make()
    {
        return new Image($this->getProcessor());
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
            $loader =  call_user_func($instantiator);
        } else {
            $loader = new DelegatingLoader([
                new FilesystemLoader,
                new RemoteLoader
            ]);
        }

        return $this->loader = $loader;


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
     * @return mixed
     */
    abstract protected function createProcessor();
}
