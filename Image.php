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

/**
 * @class Image implements ImageInterface
 * @see ImageInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Image implements ImageInterface
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
     * @var array
     */
    protected $arguments;

    /**
     * targetSize
     *
     * @var array
     */
    protected $targetSize;

    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * mode
     *
     * @var int
     */
    protected $mode;

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
    }

    /**
     * create
     *
     * @param mixed $source
     * @param mixed $driver
     *
     * @access public
     * @return Image
     */
    public static function create($source = null, $driver = self::DRIVER_IMAGICK)
    {
        $image = static::getFactory($driver)->make();

        if ($source) {
            $image->source($source);
        }

        return $image;
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
        $this->setMode(ProcessorInterface::IM_CROP);
        $this->setTargetSize($width, $height);
        $this->setArguments([(int)$gravity, $background]);

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
        $this->setMode(ProcessorInterface::IM_SCALECROP);
        $this->setTargetSize($width, $height);
        $this->setArguments([$gravity]);

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
        $this->setMode(ProcessorInterface::IM_RSIZEFIT);
        $this->setTargetSize($width, $height);
        $this->setArguments([]);

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
        $this->setMode(ProcessorInterface::IM_RESIZE);
        $this->setTargetSize($width, $height);
        $this->setArguments([]);

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
        $this->setMode(ProcessorInterface::IM_RSIZEPERCENT);
        $this->setTargetSize($percent);
        $this->setArguments([]);

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
        $this->setMode(ProcessorInterface::IM_RSIZEPXCOUNT);
        $this->setTargetSize($pixel);
        $this->setArguments([]);

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
        $this->filters = clone($this->filters);

        $this->source = null;
        $this->arguments = [];

        $this->getProcessor()->close();
    }

    protected function getProcessor()
    {
        return $this->processor;
    }

    /**
     * setMode
     *
     * @param mixed $mode
     *
     * @access protected
     * @return void
     */
    protected function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * setTargetSize
     *
     * @param mixed $width
     * @param mixed $height
     *
     * @access protected
     * @return void
     */
    protected function setTargetSize($width = null, $height = null)
    {
        $this->targetSize = [$width, $height];
    }

    /**
     * setArguments
     *
     * @param array $arguments
     *
     * @access protected
     * @return void
     */
    protected function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * compileExpression
     *
     *
     * @access protected
     * @return mixed
     */
    protected function compileExpression()
    {
        list ($width, $height) = array_pad($this->targetSize, 2, null);

        $parts = compact('width', 'height');
        $parts['mode'] = $this->mode;

        foreach ($this->arguments as $i => $arg) {
            if (0 === $i) {
                $parts['gravity'] = $arg;
            } elseif (1 === $i && $this->isColor($arg)) {
                $parts['background'] = trim((string) $arg);
            }
        }

        return $parts;
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
     * @param string $driver
     *
     * @throws \InvalidArgumentException if driver is not defined.
     * @return DriverInterface
     */
    protected static function getFactory($driver)
    {
        switch ($driver) {
            case self::DRIVER_GD:
                return new GdFactory;
            case self::DRIVER_IM:
                return new ImFactory;
            case self::DRIVER_IMAGICK:
                return new ImagickFactory;
            default:
                throw new \InvalidArgumentException(sprintf('invalid driver %s', $driver));
        }
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
