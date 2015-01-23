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
use Thapp\Image\Driver\SourceInterface;
use Thapp\Image\Palette\Rgb as PaletteRgb;
use Thapp\Image\Palette\Greyscale as PaletteGrey;
use Thapp\Image\Palette\Cmyk as PaletteCmyk;
use Thapp\Image\Exception\SourceException;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw SourceException::resource();
        }

        $imagick = new Imagick;

        try {
            $imagick->readImageFile($resource);
        } catch (\Exception $e) {
            $imagick->destroy();

            throw SourceException::resource($e);
        }

        return new Image($imagick);

    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImage($file);
        } catch (\Exception $e) {
            $imagick->destroy();

            throw SourceException::resource($e);
        }

        return new Image($imagick);
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImageBlob($image);
        } catch (\Exception $e) {
            $imagick->destroy();

            throw SourceException::resource($e);
        }

        return new Image($imagick);
    }

    /**
     * getColorPalette
     *
     * @param Imagick $image
     *
     * @return PaletteInterface
     */
    protected function getColorPalette(Imagick $image)
    {
        switch ($image->getImageColorSpace()) {
            case Imagick::COLORSPACE_RGB:
            case Imagick::COLORSPACE_SRGB:
                return new PaletteRgb;
            case Imagick::COLORSPACE_GRAY:
                return new PaletteGreyscale;
            case Imagick::COLORSPACE_CMYK:
                return new PaletteCmyk;
            default:
                throw new \InvalidArgumentException('Unsupported color space.');
                break;
        }
    }
}
