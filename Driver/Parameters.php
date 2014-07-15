<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

/**
 * @class Parameters
 * @package Thapp\Image\Driver
 * @version $Id$
 */
class Parameters
{
    const P_SEPARATOR = '/';

    private $params;

    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->params = [];
    }

    /**
     * setHeight
     *
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    public function setTargetSize($width = null, $height = null)
    {
        $this->params['width']  = $width;
        $this->params['height'] = $height;
    }

    /**
     * setMode
     *
     * @param int $mode
     *
     * @return void
     */
    public function setMode($mode)
    {
        $this->params['mode']  = (int)$mode;
    }

    /**
     * setGravity
     *
     * @param int $gravity
     *
     * @return void
     */
    public function setGravity($gravity = null)
    {
        $this->params['gravity'] = $gravity;
    }

    /**
     * setBackground
     *
     * @param string $color
     *
     * @return void
     */
    public function setBackground($background = null)
    {
        if (null !== $background && $this->isColor($background)) {
            $this->params['background'] = $background;
        }
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        $params = array_merge(static::defaults(), $this->params);

        return static::sanitize(
            $params['mode'],
            $params['width'],
            $params['height'],
            $params['gravity'],
            $params['background']
        );
    }

    public function asString($separator = self::P_SEPARATOR)
    {
        return implode($separator, array_filter(array_values($this->all()), function ($val) {
            return null !== $val;
        }));
    }

    public function setFromString($str, $separator = self::P_SEPARATOR)
    {
        $this->params = static::parseString($str, $separator);
    }

    /**
     * parseString
     *
     * @param string $paramString
     * @param string $separator
     *
     * @return array
     */
    public static function parseString($paramString, $separator = self::P_SEPARATOR)
    {
        list ($mode, $width, $height, $gravity, $background) = array_map(function ($value, $key = null) {
            return is_numeric($value) ? (int)$value : $value;
        }, array_pad(explode($separator, $paramString), 5, null));

        return static::sanitize($mode, $width, $height, $gravity, $background);
    }

    /**
     * isColor
     *
     * @param mixed $color
     *
     * @return bool
     */
    protected function isColor($color)
    {
        return (bool)preg_match('#^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$#', $color);
    }

    /**
     * sanitize
     *
     * @param int $mode
     * @param int $width
     * @param int $height
     * @param int $gravity
     * @param string $background
     *
     * @access private
     * @return array
     */
    private static function sanitize($mode = null, $width = null, $height = null, $gravity = null, $background = null)
    {
        if (null === $mode) {
            $mode = 0;
        }

        if (2 !== $mode && 3 !== $mode) {
            $gravity = null;
        } elseif (null === $gravity) {
            $gravity = 5;
        }

        if ($mode !== 3) {
            $background = null;
        }

        if (4 < $mode || 0 === $mode) {
            $height     = null;
            $gravity    = null;
        }

        if (0 == $mode) {
            $width = null;
        }

        $width  = ($mode !== 1 && $mode !== 2) ? $width : (int)$width;
        $height = ($mode !== 1 && $mode !== 2) ? $height : (int)$height;

        return compact('mode', 'width', 'height', 'gravity', 'background');
    }

    private static function defaults()
    {
        return ['mode' => null, 'width' => null, 'height' => null, 'gravity' => null, 'background' => null];
    }

    /**
     * fromString
     *
     * @param mixed $paramString
     * @param mixed $separator
     *
     * @access public
     * @return Parameters
     */
    public static function fromString($paramString, $separator = self::P_SEPARATOR)
    {
        return new static(static::parseString($paramString, $separator));
    }
}
