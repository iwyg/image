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

use \Thapp\Image\Factory\ImFactory;
use \Thapp\Image\Factory\ImagickFactory;

/**
 * Facade class for an Imageprocessor implementation
 *
 * @class Image Image
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Image
{
    const DRIVER_IM = 'im';

    const DRIVER_IMAGICK = 'imagick';

    const DRIVER_GD = 'gb';

    private $filters;

    private $arguments;

    private $targetSize;

    private $mode;

    /**
     * __construct
     *
     * @param ProcessorInterface $processor
     *
     * @access public
     * @return mixed
     */
    public function __construct(ProcessorInterface $processor)
    {
        $this->filters = [];
        $this->arguments = [];
        $this->targetSize = [];

        $this->processor = $processor;
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
     * getFactory
     *
     * @param mixed $driver
     *
     * @throws \InvalidArgumentException if driver is not defined.
     * @access protected
     * @return DriverInterface
     */
    protected static function getFactory($driver)
    {
        switch ($driver) {
            case self::DRIVER_IMAGICK:
                return new ImagickFactory;
            case self::DRIVER_IM:
                return new ImFactory;
            default:
                throw new \InvalidArgumentException(sprintf('invalid driver %s', $driver));
        }
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
        $this->processor->load($source);
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
    public function from($source)
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
        $this->processor->close();
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
        $this->processor->close();

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
        $this->filters[$name] = $options;

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
     * process
     *
     * @access protected
     * @return mixed
     */
    protected function process()
    {
        $params = $this->compileExpression();

        $params['filter'] = $this->filters;

        $this->processor->process($params);

        return $this;
    }

    /**
     * setMode
     *
     * @param mixed $mode
     *
     * @access protected
     * @return mixed
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
     * @return mixed
     */
    protected function setTargetSize($width = null, $height = null)
    {
        $this->targetSize = [$width, $height];
    }

    protected function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * compileFilterExpression
     *
     * @access private
     * @return string|null
     */
    private function compileFilterExpression()
    {
        $filters = [];

        foreach ($this->filters as $filter => $options) {
            $opt = [];

            if (is_array($options)) {

                foreach ($options as $option => $value) {
                    $opt[] = sprintf('%s=%s', $option, $value);
                }
            }
            $filters[] = sprintf('%s;%s', $filter, implode(';', $opt));
        }

        $filterString = sprintf('filter:%s', implode(':', $filters));

        return strlen($filterString) ? $filterString  : null;
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
}
