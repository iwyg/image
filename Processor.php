<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image;

use \Thapp\Image\Writer\WriterInterface;
use \Thapp\Image\Driver\DriverInterface;

/**
 * @class Processor implements ProcessorInterface
 * @see ProcessorInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Processor implements ProcessorInterface
{
    /**
     * driver
     *
     * @var \Thapp\JitImage\Driver\DriverInterface
     */
    protected $driver;

    /**
     * @var \Thapp\JitImage\Writer\WriterInterface
     *
     * @var mixed
     */
    protected $writer;

    /**
     * loaded
     *
     * @var boolean
     */
    protected $loaded;

    /**
     * compression
     *
     * @var int
     */
    protected $compression;

    /**
     * attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * Create a new instance of Image
     *
     * @param InterfaceDriver $driver
     */
    public function __construct(DriverInterface $driver, WriterInterface $writer)
    {
        $this->driver = $driver;
        $this->writer = $writer;

        $this->attributes = [];
    }

    /**
     * {@inheritDoc}
     */
    public function load($source)
    {
        return $this->loaded = $this->driver->load($source);
    }

    /**
     * writeToFile
     *
     * @param mixed $target
     *
     * @access public
     * @return mixed
     */
    public function writeToFile($target)
    {
        if (!$this->loaded) {
            throw new \BadMethodCallException('no source loaded');
        }
        return $this->writer->write($target, $this->getContents());
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $parameters)
    {
        $params = array_merge($this->defaultParams(), $parameters);

        $this->driver->setTargetSize($params['width'], $params['height']);

        switch($params['mode']) {
            case static::IM_NOSCALE:
                break;
            case static::IM_RESIZE:
                $this->resize();
                break;
            case static::IM_SCALECROP:
                $this->cropScale($params['gravity']);
                break;
            case static::IM_CROP:
                $this->crop($params['gravity'], $params['background']);
                break;
            case static::IM_RSIZEFIT:
                $this->resizeToFit();
                break;
            case static::IM_RSIZEPERCENT:
                $this->resizePercentual($params['width']);
                break;
            case static::IM_RSIZEPXCOUNT:
                $this->resizePixelCount($params['width']);
                break;
            default:
                break;
        }

        foreach ((array)$params['filter'] as $f => $parameter) {
            $this->addFilter($f, (array)$parameter);
        }

        $this->driver->process();
    }

    /**
     * {@inheritDoc}
     */
    public function setQuality($quality)
    {
        $this->driver->setQuality($quality);
    }

    /**
     * {@inheritDoc}
     */
    public function setFileFormat($format)
    {
        return $this->driver->setOutputType($format);
    }

    /**
     * {@inheritDoc}
     */
    public function getContents()
    {
        return $this->driver->getImageBlob();
    }

    /**
     * {@inheritDoc}
     */
    public function getFileFormat()
    {
        return $this->driver->getOutputType();
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceFormat()
    {
        return $this->driver->getSourceType(true);
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceMimeTime()
    {
        return $this->driver->getSourceType(false);
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType()
    {
        return $this->driver->getOutputMimeType();
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->driver->getSource();
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        if (!$this->loaded) {
            return;
        }

        $this->loaded = false;
        return $this->driver->clean();
    }

    /**
     * isProcessed
     *
     * @access public
     * @return mixed
     */
    public function isProcessed()
    {
        return $this->driver->isProcessed();
    }

    /**
     * getLastModTime
     *
     * @access public
     * @return mixed
     */
    public function getLastModTime()
    {
        if ($this->isProcessed()) {
            return time();
        }

        return filemtime($this->driver->getSource());
    }

    /**
     * addFilter
     *
     * @access public
     * @return mixed
     */
    protected function addFilter($filter, array $options = [])
    {
        $this->driver->filter($filter, $options);
    }

    /**
     * mode 1 filter: scale
     *
     * @param FilterInterface $filter
     * @access public
     * @return mixed
     */
    protected function resize()
    {
        return $this->driver->filter('resize', func_get_args());
    }

    /**
     * mode 2 filter: cropScale
     *
     * @param mixed $width
     * @param mixed $height
     * @param mixed $gravity
     * @access public
     * @return mixed
     */
    protected function cropScale()
    {
        return $this->driver->filter('cropScale', func_get_args());
    }

    /**
     * mode 3 filter: crop
     *
     * @param FilterInterface $filter
     * @access public
     * @return mixed
     */
    protected function crop()
    {
        return $this->driver->filter('crop', func_get_args());
    }

    /**
     * mode 4 filter: resizeToFit
     *
     * @access public
     * @return void
     */
    protected function resizeToFit()
    {
        return $this->driver->filter('resizeToFit', func_get_args());
    }

    /**
     * mode 5 filte: percentualScale
     *
     * @access protected
     * @return void
     */
    protected function resizePercentual()
    {
        return $this->driver->filter('percentualScale', func_get_args());
    }

    /**
     * mode 6 filte: resizePixelCount
     *
     * @access protected
     * @return void
     */
    protected function resizePixelCount()
    {
        return $this->driver->filter('resizePixelCount', func_get_args());
    }

    /**
     * defaultParams
     *
     * @access protected
     * @return array
     */
    protected function defaultParams()
    {
        return [
            'mode'       => 0,
            'width'      => 100,
            'height'     => 100,
            'gravity'    => 0,
            'quality'    => 80,
            'background' => null,
            'filter'     => null
        ];
    }
}
