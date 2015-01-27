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
use ImagickException;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Grayscale;
use Thapp\Image\Driver\AbstractSource;
use Thapp\Image\Exception\ImageException;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source extends AbstractSource
{
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource) || 'stream' !== get_resource_type($resource)) {
            throw SourceException::resource();
        }

        $imagick = new Imagick;

        try {
            $imagick->readImageFile($resource);
        } catch (ImagickException $e) {
            $imagick->destroy();

            throw ImageException::read($e);
        }

        return new Image($imagick, $this->getColorPalette($imagick), $this->getReader()->readFromStream($resource));

    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImage($file);
        } catch (ImagickException $e) {
            $imagick->destroy();

            throw ImageException::load($e);
        }

        return new Image($imagick, $this->getColorPalette($imagick), $this->getReader()->readFromFile($file));
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImageBlob($image);
        } catch (ImagickException $e) {
            $imagick->destroy();

            throw ImageException::create($e);
        }

        return new Image($imagick, $this->getColorPalette($imagick), $this->getReader()->readFromBlob($image));
    }

    /**
     * getColorPalette
     *
     * @param Imagick $imagick
     *
     * @return Thapp\Image\Color\PaletteInterface
     */
    protected function getColorPalette(Imagick $imagick)
    {
        switch($imagick->getImageColorspace()) {
            case Imagick::COLORSPACE_RGB:
            case Imagick::COLORSPACE_SRGB:
                return new Rgb;
            case Imagick::COLORSPACE_CMYK:
                return new Cmyk;
            case Imagick::COLORSPACE_GRAY:
                return new Grayscale;
            default:
                break;
        }

        throw new ImageException('Unsupported color space.');
    }
}
