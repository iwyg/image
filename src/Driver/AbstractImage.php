<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Exception\ImageException;
use Thapp\Image\Geometry\GravityInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Filter\DriverAwareFilterInterface;

/**
 * @class AbstractImage
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractImage implements ImageInterface
{
    /** @var array */
    protected static $orients;

    /** @var string */
    protected $format;

    /** @var \Thapp\Image\Driver\FramesInterface */
    protected $frames;

    /** @var \Thapp\Image\Geometry\GravityInterface */
    protected $gravity;

    /** @var \Thapp\Image\Color\Palette\PaletteInterface */
    protected $palette;

    /** @var \Thapp\Image\Info\MetaDataInterface */
    protected $meta;

    /** @var \Thapp\Image\Driver\EditInterface */
    protected $edit;

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * {@inheritdoc}
     */
    public function backup($name = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function restore($name = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat($format)
    {
        $this->format = $this->mapFormat($format);
    }

    /**
     * {@inheritdoc}
     */
    public function setGravity(GravityInterface $gravity)
    {
        $this->gravity = $gravity;
    }

    /**
     * {@inheritdoc}
     */
    public function getGravity()
    {
        return $this->gravity ?: new Gravity(GravityInterface::GRAVITY_NORTHWEST);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return new Size($this->getWidth(), $this->getHeight());
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return strtolower($this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function applyPalette(PaletteInterface $palette)
    {
        if ($this->palette->getConstant() === ($const = $palette->getConstant())) {
            return false;
        }

        if (!$this->supportsPalette($palette)) {
            throw new ImageException(sprintf('Image doesn\'t supports palette of type %s.', get_class($palette)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPalette()
    {
        return $this->palette;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function edit()
    {
        if (null === $this->edit) {
            /** @var EditInterface */
            $this->edit = $this->newEdit();
        }

        return $this->edit;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrames()
    {
        return 1 < count($this->frames());
    }

    /**
     * {@inheritdoc}
     */
    public function frames()
    {
        return $this->frames;
    }

    /**
     * {@inheritdoc}
     */
    public function coalesce()
    {
        return $this->frames()->coalesce();
    }

    /**
     * {@inheritdoc}
     */
    public function filter(FilterInterface $filter)
    {
        if ($filter instanceof DriverAwareFilterInterface && !$filter->supports($this)) {
            throw new \LogicException('Invalid filter for Image driver.');
        }

        $filter->apply($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrientation()
    {
        return $this->mapOrientation($this->getMetaData()->get('ifd0.Orientation', 0));
    }

    /**
     * {@inheritdoc}
     */
    public function strip()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($path, $format = null, array $options = [])
    {
        $content = $this->getBlob($format, $options);
        if (false !== @file_put_contents($path, $content)) {
            return true;
        }

        throw new ImageException(sprintf('Couldn\'t save image to "%s".', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function write($stream, $format = null, array $options = [])
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new ImageException('Couldn\'t write image data to stream. Stream is invalid.');
        }

        // NOTE: fwrite doesn't return false if stream is read only. Instead it just
        // writes 0 bytes.
        if (0 !== fwrite($stream, $this->getBlob($format, $options))) {
            return true;
        }

        throw new ImageException('Couldn\'t write image data to stream. Stream is not writable.');
    }


    /**
     * newEdit
     *
     * @return \Thapp\Image\Driver\EditInterface
     */
    abstract protected function newEdit();

    /**
     * getOutputFormat
     *
     * @param mixed $format
     * @param array $options
     *
     * @return void
     */
    protected function getOutputFormat($format = null, array &$options = [])
    {
        if (null === $format) {
            $format = isset($options['format']) ? $this->mapFormat($options['format']) : $this->getFormat();
        }

        return $options['format'] = $format;
    }

    /**
     * mapFormat
     *
     * @param string $format
     *
     * @return string
     */
    protected function mapFormat($format)
    {
        switch ($fmt = strtolower($format)) {
            case 'jpg':
            case 'ejpg':
            case self::FORMAT_JPEG:
                return self::FORMAT_JPEG;
            case 'tif':
            case self::FORMAT_TIFF:
                return self::FORMAT_TIFF;
            default:
                break;
        }

        return $fmt;
    }

    /**
     * getOption
     *
     * @param array $options
     * @param mixed $option
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getOption(array $options, $option, $default = null)
    {
        if (array_key_exists($option, $options)) {
            return $options[$option];
        }

        return $default;
    }

    /**
     * supportsPalette
     *
     * @param PaletteInterface $palette
     *
     * @return void
     */
    protected function supportsPalette(PaletteInterface $palette)
    {
        return false;
    }

    ///**
    // * formatToInterlace
    // *
    // * @param string $format
    // *
    // * @return void
    // */
    //protected function formatToInterlace($format)
    //{
    //    $map = [
    //        'gif'  => self::INTERLACE_GIF,
    //        'jpeg' => self::INTERLACE_JPEG,
    //        'png'  => self::INTERLACE_PNG
    //    ];

    //    if (isset($map[$format])) {
    //        return $map[$format];
    //    }

    //    return self::INTERLACE_UNDEFINED;
    //}

    /**
     * mapOrientation
     *
     * @param mixed $orient
     *
     * @return void
     */
    protected function mapOrientation($orient = null)
    {
        $orients = static::getOrientations($orient);

        if (isset($orients[$orient])) {
            return $orients[$orient];
        }

        return self::ORIENT_UNDEFINED;
    }

    /**
     * &getOrient
     *
     * @param mixed $orient
     *
     * @return void
     */
    protected static function &getOrientations($orient)
    {
        if (null === static::$orients) {
            static::$orients = [
                self::ORIENT_UNDEFINED,
                self::ORIENT_TOPLEFT,
                self::ORIENT_TOPRIGHT,
                self::ORIENT_BOTTOMRIGHT,
                self::ORIENT_BOTTOMLEFT,
                self::ORIENT_LEFTTOP,
                self::ORIENT_RIGHTTOP,
                self::ORIENT_RIGHTBOTTOM,
                self::ORIENT_LEFTBOTTOM,
            ];
        }

        return static::$orients;
    }

    /**
     * getInterlaceScheme
     *
     * @param string $format
     *
     * @return array
     */
    protected function getInterlaceScheme($scheme)
    {
        $map = $this->getInterlaceMap();
        if (isset($map[$scheme])) {
            return $map[$scheme];
        }

        throw new \InvalidArgumentException(sprintf('Unknown interlace scheme "%s"', (string)$scheme));
    }

    abstract protected function &getInterlaceMap();
}
