<?php

/*
 * This File is part of the Thapp\Image\Driver\Gmagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gmagick;

use Gmagick;
use GmagickException;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Grayscale;
use Thapp\Image\Driver\AbstractSource;
use Thapp\Image\Exception\ImageExceptoion;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Gmagick
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
        $this->validateStream($resource);

        $meta = stream_get_meta_data($resource);

        try {
            // Gmagick::readImageFile may cause segfault error.
            if (isset($meta['uri']) && stream_is_local($file = $meta['uri'])) {
                return $this->load($file);
            }
            return $this->create(stream_get_contents($resource));
        } catch (ImageException $e) {
            throw ImageException::read($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $gmagick = new Gmagick;

        try {
            $gmagick->readImage($file);
        } catch (GmagickException $e) {
            $gmagick->destroy();
            throw ImageException::load($e);
        }

        return new Image($gmagick, $this->getColorPalette($gmagick), $this->getReader()->readFromFile($file));
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $gmagick = new Gmagick;

        try {
            $gmagick->readImageBlob($image);
        } catch (GmagickException $e) {
            $gmagick->destroy();
            throw ImageException::create($e);
        }

        return new Image($gmagick, $this->getColorPalette($gmagick), $this->getReader()->readFromBlob($image));
    }

    /**
     * getColorPalette
     *
     * @param Gmagick $image
     *
     * @return PaletteInterface
     */
    protected function getColorPalette(Gmagick $image)
    {
        switch ($image->getImageColorSpace()) {
            case Gmagick::COLORSPACE_RGB:
            case Gmagick::COLORSPACE_SRGB:
                return new Rgb;
            case Gmagick::COLORSPACE_CMYK:
                return new Cmyk;
            case Gmagick::COLORSPACE_GRAY:
                return new Grayscale;
            default:
                throw new ImageException('Unsupported color space.');
                break;
        }
    }
}
