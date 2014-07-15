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
     * @var FilterExpression
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
     * @var CacheInterface
     */
    protected $cache;

    protected $resource;

    /**
     * Constructor.
     *
     * @param ProcessorInterface $processor
     * @param CacheInterface $cache
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
     * getProcessor
     *
     * @return ProcessorInterface
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * getImageCache
     *
     * @return CacheInterface
     */
    public function getImageCache()
    {
        return $this->cache;
    }

    /**
     * source
     *
     * @param mixed $source
     *
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
     * @return mixed
     */
    public function save($target)
    {
        $this->process();
        $this->processor->writeToFile($target, $this->getResource());
        $this->close();
    }

    /**
     * Get the image resource if loaded from cache
     */
    protected function getResource()
    {
        return $this->resource;
    }

    /**
     * getImageData
     *
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
        return $this->addFilter('convert', ['f' => ProcessorInterface::FORMAT_JPG]);
    }

    /**
     * toPng
     *
     * @access public
     * @return mixed
     */
    public function toPng()
    {
        return $this->addFilter('convert', ['f' => ProcessorInterface::FORMAT_PNG]);
    }

    /**
     * toGif
     *
     * @return mixed
     */
    public function toGif()
    {
        return $this->addFilter('convert', ['f' => ProcessorInterface::FORMAT_GIF]);
    }

    /**
     * filter
     *
     * @param string $name
     * @param array $options
     *
     * @return ImageInterface
     */
    public function addFilter($name, $options = [])
    {
        $this->filters->addFilter($name, $options);

        return $this;
    }

    /**
     * filterExpression
     *
     * @param string $expr
     *
     * @return ImageInterface
     */
    public function filter($expr)
    {
        $this->filters = clone($this->filters);
        $this->filters->setExpression($expr);

        return $this;
    }

    /**
     * Pass through
     *
     * @return ImageInterface
     */
    public function get()
    {
        $this->parameters->setMode(ProcessorInterface::IM_NOSCALE);

        return $this;
    }

    /**
     * resize
     *
     * @param mixed $width
     * @param mixed $height
     *
     * @return ImageInterface
     */
    public function resize($width, $height)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RESIZE);
        $this->parameters->setTargetSize($width, $height);

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
     * @return ImageInterface
     */
    public function crop($width, $height, $gravity = 5, $background = null)
    {
        $this->parameters->setMode(ProcessorInterface::IM_CROP);
        $this->parameters->setTargetSize($width, $height);
        $this->parameters->setGravity($gravity);
        $this->parameters->setBackground($background);

        return $this->process();
    }

    /**
     * callCropAndResize
     *
     * @param int $width
     * @param int $height
     * @param int $gravity
     *
     * @return ImageInterface
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
     * @return ImageInterface
     */
    public function fit($width, $height)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RSIZEFIT);
        $this->parameters->setTargetSize($width, $height);

        return $this;
    }

    /**
     * scale
     *
     * @param mixed $percent
     *
     * @return ImageInterface
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
     *
     * @return ImageInterface
     */
    public function pixel($pixel)
    {
        $this->parameters->setMode(ProcessorInterface::IM_RSIZEPXCOUNT);
        $this->parameters->setTargetSize($pixel);

        return $this;
    }

    /**
     * setImageCache
     *
     * @param CacheInterface $cache
     *
     * @return void
     */
    public function setImageCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * process
     *
     * @return mixed
     */
    protected function process()
    {
        if ($this->processor->isProcessed()) {
            return $this;
        }

        $params = $this->compileExpression();

        if (null === $this->cache) {
            $this->doProcess($params);

            return $this;
        }

        if ($this->cache->has($key = $this->getCacheKey($this->parameters, $this->filters))) {
            $this->loadFromCache($key);

            return $this;
        }

        $this->doProcess($params);
        $this->cache->set($key, $this->processor);

        return $this;
    }

    /**
     * loadFromCache
     *
     * @param array $params
     *
     * @return string
     */
    protected function loadFromCache($key)
    {
        $this->resource = $this->cache->get($key, CacheInterface::CONTENT_RESOURCE);
    }

    /**
     * doProcess
     *
     * @param array $params
     *
     * @return void
     */
    protected function doProcess(array $params)
    {
        $this->processor->load($this->source);
        $params['filter'] = $this->filters->toArray();
        $this->processor->process($params);
    }

    /**
     * close
     *
     * @return void
     */
    protected function close()
    {
        $this->filters    = clone($this->filters);
        $this->parameters = clone($this->parameters);

        $this->source = null;
        $this->resource = null;
        $this->arguments = [];

        $this->getProcessor()->close();
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
     * @return string
     */
    protected function getCacheKey(Parameters $params, FilterExpression $filter)
    {
        $fingerprint = $this->getImageFingerPrint($params, $filter);

        return $this->cache->createKey(
            $this->source,
            $fingerprint,
            null,
            pathinfo($this->source, PATHINFO_EXTENSION)
        );
    }

    /**
     * getImageFingerPrint
     *
     * @return string
     */
    protected function getImageFingerPrint(Parameters $params, FilterExpression $filters)
    {
        $filter = $filters->compile();

        return $params->asString() . (0 < strlen($filter) ? '/filter:'.$filter : '');
    }
}
