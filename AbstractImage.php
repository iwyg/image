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

use \Thapp\Image\Factory\GdFactory;
use \Thapp\Image\Factory\ImFactory;
use \Thapp\Image\Factory\ImagickFactory;
use \Thapp\Image\Cache\CacheInterface;
use \Thapp\Image\Cache\FilesystemCache;
use \Thapp\Image\Filter\FilterExpression;
use \Thapp\Image\Driver\Parameters;

/**
 * @class Image implements ImageInterface
 * @see ImageInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractImage implements ImageInterface
{
    /**
     * filters
     *
     * @var array
     */
    protected $filters;

    /**
     * arguments
     *
     * @var Parameters
     */
    protected $parameters;

    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * cache
     *
     * @var mixed
     */
    protected $cache;

    /**
     * @param ProcessorInterface $processor
     *
     * @access public
     * @return mixed
     */
    public function __construct(ProcessorInterface $processor, CacheInterface $cache = null)
    {
        $this->filters = new FilterExpression([]);

        $this->processor = $processor;
        $this->cache = $cache;

        $this->arguments = [];
        $this->targetSize = [];

        $this->parameters = new Parameters;
    }

    /**
     * source
     *
     * @param mixed $source
     *
     * @access public
     * @return mixed
     */
    public function source($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * from
     *
     * @param mixed $source
     *
     * @access public
     * @return mixed
     */
    public function load($source)
    {
        return $this->source($source);
    }

    /**
     * save
     *
     * @param mixed $target
     *
     * @access public
     * @return mixed
     */
    public function save($target)
    {
        $this->process();
        $this->processor->writeToFile($target);
        $this->close();
    }

    /**
     * getImageData
     *
     * @access public
     * @return string
     */
    public function getImageData()
    {
        $this->process();

        $content = $this->processor->getContents();
        $this->close();

        return $content;
    }

    /**
     * quality
     *
     * @param int $quality
     *
     * @access public
     * @return Image
     */
    public function quality($quality = 80)
    {
        $this->processor->setQuality((int)$quality);

        return $this;
    }

    /**
     * toJpeg
     *
     * @access public
     * @return mixed
     */
    public function toJpeg()
    {
        return $this->filter('convert', ['f' => ProcessorInterface::FORMAT_JPG]);
    }

    /**
     * toPng
     *
     * @access public
     * @return mixed
     */
    public function toPng()
    {
        return $this->filter('convert', ['f' => ProcessorInterface::FORMAT_PNG]);
    }

    /**
     * toGif
     *
     * @access public
     * @return mixed
     */
    public function toGif()
    {
        return $this->filter('convert', ['f' => ProcessorInterface::FORMAT_GIF]);
    }

    /**
     * filter
     *
     * @param mixed $name
     * @param mixed $options
     *
     * @access public
     * @return Image
     */
    public function filter($name, $options = [])
    {
        $this->filters->addFilter($name, $options);

        return $this;
    }

    /**
     * crop
     *
     * @param mixed $width
     * @param mixed $height
     * @param int $gravity
     * @param mixed $background
     *
     * @access public
     * @return mixed
     */
    public function crop($width, $height, $gravity = 5, $background = null)
    {
        $this->parameters->setMode(ProcessorInterface::IM_CROP);
        $this->parameters->setTargetSize($width, $height);
        $this->parameters->setGravity($gravity);
        $this->parameters->setBackground($gravity);

        return $this->process();
    }

    /**
     * callCropAndResize
     *
     * @param int $width
     * @param int $height
     * @param int $gravity
     *
     * @access protected
     * @return void
     */
    public function cropAndResize($width, $height, $gravity)
    {
        $this->parameters->setMode(ProcessorInterface::IM_SCALECROP);
        $this->parameters->setTargetSize($width, $height);
        $this->parameters->setGravity($gravity);

        return $this;
    }

    /**
     * callFit
     *
     * @param int $width
     * @param int $height
     *
     * @access protected
     * @return void
     */
    public function fit($width, $height)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RSIZEFIT);
        $this->parameters->setTargetSize($width, $height);

        return $this;
    }

    /**
     * resize
     *
     * @param mixed $width
     * @param mixed $height
     *
     * @access public
     * @return mixed
     */
    public function resize($width, $height)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RESIZE);
        $this->parameters->setTargetSize($width, $height);

        return $this;
    }

    /**
     * scale
     *
     * @param mixed $percent
     *
     * @access public
     * @return mixed
     */
    public function scale($percent)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RSIZEPERCENT);
        $this->parameters->setTargetSize($percent);

        return $this;
    }

    /**
     * pixel limit
     *
     * @param mixed $width
     * @param mixed $height
     * @access protected
     * @return void
     */
    public function pixel($pixel)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RSIZEPXCOUNT);
        $this->parameters->setTargetSize($pixel);

        return $this;
    }

    /**
     * isColor
     *
     * @param mixed $color
     *
     * @access protected
     * @return boolean
     */
    protected function isColor($color)
    {
        return (bool)preg_match('#^[0-9a-fA-F]{3}|^[0-9a-fA-F]{6}#', $color);
    }

    /**
     * setImageCache
     *
     * @param CacheInterface $cache
     *
     * @access public
     * @return mixed
     */
    public function setImageCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * process
     *
     * @access protected
     * @return mixed
     */
    protected function process()
    {
        if ($this->processor->isProcessed()) {
            return;
        }

        $params = $this->compileExpression();

        if (null === $this->cache) {
            $this->doProcess($params);

            return $this;
        }

        if ($this->cache->has($key = $this->getCacheKey($params, $this->filters))) {
            $this->loadFromCache($key);

            return $this;
        }

        $this->doProcess($params);
        $this->cache->set($key, $this->processor->getContents());

        return $this;
    }

    /**
     * loadFromCache
     *
     * @param array $params
     *
     * @access protected
     * @return string
     */
    protected function loadFromCache($key)
    {
        $source = $this->cache->getSource($key);
        $this->processor->load($source);

        return $source;
    }

    /**
     * doProcess
     *
     * @param array $params
     *
     * @access protected
     * @return void
     */
    protected function doProcess(array $params)
    {
        $this->processor->load($this->source);
        $params['filter'] = $this->filters;
        $this->processor->process($params);
    }

    /**
     * close
     *
     *
     * @access protected
     * @return void
     */
    protected function close()
    {
        $this->filters    = clone($this->filters);
        $this->parameters = clone($this->parameters);

        $this->source = null;
        $this->arguments = [];

        $this->getProcessor()->close();
    }

    /**
     * getProcessor
     *
     * @return ProcessorInterface
     */
    protected function getProcessor()
    {
        return $this->processor;
    }

    /**
     * compileExpression
     *
     * @return array
     */
    protected function compileExpression()
    {
        return $this->parameters->all();
    }

    /**
     * getCacheKey
     *
     * @param array $params
     * @param array $filter
     *
     * @access protected
     * @return string
     */
    protected function getCacheKey(array $params, array $filter)
    {
        $fingerprint = $this->getImageFingerPrint($params, $filter);

        return $this->cache->createKey($this->source, $fingerprint, null, pathinfo($this->source, PATHINFO_EXTENSION));
    }

    /**
     * getImageFingerPrint
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getImageFingerPrint(array $params, array $filters)
    {
        return rtrim(implode('/', $params) . '/'.$this->compileFilterExpression($filters), '/');
    }

    /**
     * compileFilterExpression
     *
     * @return string|null
     */
    private function compileFilterExpression(array $filter)
    {
        return (new FilterExpression($this->filters))->compile();
    }
}
