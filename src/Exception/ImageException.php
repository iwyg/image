<?php

/*
 * This File is part of the Thapp\Image\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Exception;

/**
 * @class ImageException
 *
 * @package Thapp\Image\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageException extends \Exception
{
    /**
     * read
     *
     * @param \Exception $previous
     *
     * @return ImageException
     */
    public static function read(\Exception $previous = null)
    {
        return new static('Reading image from file handle failed.', $previous ? $previous->getCode() : null, $previous);
    }

    /**
     * load
     *
     * @param \Exception $previous
     *
     * @return ImageException
     */
    public static function load(\Exception $previous = null)
    {
        return new static('Loading image from file failed.', $previous ? $previous->getCode() : null, $previous);
    }

    /**
     * resource
     *
     * @param \Exception $previous
     *
     * @return ImageException
     */
    public static function create(\Exception $previous = null)
    {
        return new static('Creating image resource failed.', $previous ? $previous->getCode() : null, $previous);
    }

    /**
     * resource
     *
     * @param \Exception $previous
     *
     * @return ImageException
     */
    public static function resource(\Exception $previous = null)
    {
        return new static('Supplied resource is invalid.', $previous ? $previous->getCode() : null, $previous);
    }

    /**
     * resource
     *
     * @param \Exception $previous
     *
     * @return ImageException
     */
    public static function output(\Exception $previous = null)
    {
        return new static('Can\'t generate image contents.', $previous ? $previous->getCode() : null, $previous);
    }
}
