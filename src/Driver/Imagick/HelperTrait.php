<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Imagick;
use ImagickPixel;
use ImagickException;
use Thapp\Image\Color\Parser;
use Thapp\Image\Color\CmykInterface;
use Thapp\Image\Color\ColorInterface;

/**
 * @class ImagickHelperTrait
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait HelperTrait
{
    private static $colorMap = [
        ColorInterface::CHANNEL_RED     => Imagick::COLOR_RED,
        ColorInterface::CHANNEL_GREEN   => Imagick::COLOR_GREEN,
        ColorInterface::CHANNEL_BLUE    => Imagick::COLOR_BLUE,
        ColorInterface::CHANNEL_ALPHA   => Imagick::COLOR_ALPHA,
        ColorInterface::CHANNEL_CYAN    => Imagick::COLOR_CYAN,
        ColorInterface::CHANNEL_MAGENTA => Imagick::COLOR_MAGENTA,
        ColorInterface::CHANNEL_YELLOW  => Imagick::COLOR_YELLOW,
        ColorInterface::CHANNEL_KEY     => Imagick::COLOR_BLACK,
        ColorInterface::CHANNEL_GRAY    => Imagick::COLOR_RED
    ];

    private static $matteTypes = [
        Imagick::IMGTYPE_PALETTEMATTE => true, Imagick::IMGTYPE_COLORSEPARATIONMATTE => true,
        Imagick::IMGTYPE_TRUECOLORMATTE => true, Imagick::IMGTYPE_GRAYSCALEMATTE => true
    ];

    /**
     * isMatteImage
     *
     * @param Imagick $image
     *
     * @return boolean
     */
    public function isMatteImage(Imagick $image)
    {
        try {
            $type = $image->getImageType();
        } catch (ImagickException $e) {
            return false;
        }

        return array_key_exists($type, static::$matteTypes);
    }

    /**
     * pixelFromColor
     *
     * @param ColorInterface $color
     *
     * @return void
     */
    private function pixelFromColor(ColorInterface $color)
    {
        $px = new ImagickPixel;

        if ($color instanceof CmykInterface) {
            // setting cmyk colors doesn't work at all, we have to convert it back
            // ot RGB:
            $rgb = Parser::toRgb($color->getColor());
            $px->setColor(vsprintf('rgba(%d,%d,%d,%s)', $rgb));
        } else {
            $px->setColor($str = (string)$color);
            $px->setColorValue(Imagick::COLOR_ALPHA, $color->getAlpha());
        }

        return $px;
    }

    /**
     * pixelToColor
     *
     * @param ImagickPixel $px
     *
     * @return void
     */
    private function colorFromPixel(ImagickPixel $px)
    {
        $colorMap =& static::$colorMap;
        $multiply = $this->palette instanceof CmykPaletteInterface ? 100 : 255;

        $colors = array_map(function ($color) use ($colorMap, $px, $multiply) {
            if (!isset($colorMap[$color])) {
                throw new \RuntimeException;
            }

            $value = $px->getColorValue($colorMap[$color]);

            return ColorInterface::CHANNEL_ALPHA === $color ? (float)$value : ($value * $multiply);

        }, $keys = $this->palette->getDefinition());

        return $this->palette->getColor(array_combine($keys, $colors));
    }
}
