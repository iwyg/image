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
class Image extends AbstractImage
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
}
