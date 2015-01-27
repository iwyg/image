<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Filter\FilterInterface;
use Thapp\Image\Filter\DriverAwareFilterInterface;
use Thapp\Image\Color\ColorInterface;

/**
 * @class AbstractImage
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractImage implements ImageInterface
{
    protected $edit;
    protected $format;
    protected $frames;
    protected $gravity;
    protected $palette;
    protected static $orients;

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
        return new Box($this->getWidth(), $this->getHeight());
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
    public function edit()
    {
        if (null === $this->edit) {
            $this->edit = $this->newEdit();
        }

        return $this->edit;
    }

    /**
     * {@inheritdoc}
     */
    public function save($path, $format = null, array $options = [])
    {
        if (false !== @file_put_contents($path, $this->get($format, $options))) {
            return true;
        }

        throw new \RuntimeException(sprintf('Couldn\'t write image to given file %s.', $path));
    }

    /**
     * {@inheritdoc}
     */
    public function write($resrouce, $format = null, array $options = [])
    {
        if (!is_resource($resrouce) || 'stream' !== get_resource_type($resource)) {
            throw new \RuntimeException('Couldn\'t write image data to stream. Stream is invalid.');
        }

        if (true !== @fwrite($resource, $this->get($format, $options))) {
            throw new \RuntimeException('Couldn\'t write image data to stream. Stream is not writable.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        return false;
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
        return $this->mapOrientation($this->getMetaData()->get('ifd0.Orentation'));
    }

    /**
     * newEdit
     *
     * @return EditInterface
     */
    abstract protected function newEdit();

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
            $orients[$orient];
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
}
