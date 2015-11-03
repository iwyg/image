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

use \RuntimeException;

/**
 * @class SourceException
 *
 * @package Thapp\Image\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SourceException extends RuntimeException
{
    /**
     * read
     *
     * @param \Exception $previous
     *
     * @return SourceException
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
     * @return SourceException
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
     * @return SourceException
     */
    public static function resource(\Exception $previous = null)
    {
        return new static('Creating image resource failed.', $previous ? $previous->getCode() : null, $previous);
    }
}
