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
use Thapp\Image\Driver\SourceInterface;
use Thapp\Image\Palette\Rgb as PaletteRgb;
use Thapp\Image\Palette\Greyscale as PaletteGrey;
use Thapp\Image\Palette\Cmyk as PaletteCmyk;
use Thapp\Image\Exception\SourceException;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Gmagick
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
        if (!is_resource($resource) || 'stream' !== get_resource_type($resource)) {
            throw SourceException::resource();
        }

        /*$gmagick = new Gmagick;*/
        /*if ($gm = $gmagick->readImageFile($resource)) {*/
            /*var_dump($gm);*/

            /*$gm->clear();*/
            /*$gm->destroy();*/
            /*$gmagick->clear();*/
            /*$gmagick->destroy();*/
        /*}*/
        try {
            // Gmagick::readImageFile may cause segfault error.
            return $this->create(stream_get_contents($resource));
        } catch (SourceException $e) {
            throw SourceException::read($e->getPrevious());
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
        } catch (\Exception $e) {
            throw SourceException::load($e->getPrevious());
        }

        return new Image($gmagick);
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $gmagick = new Gmagick;
        try {
            $gmagick->readImageBlob($image);
        } catch (SourceException $e) {
            throw new SourceException('could not create image.');
        }

        return new Image($gmagick);
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
                return new PaletteRgb;
            case Gmagick::COLORSPACE_GRAY:
                return new PaletteGreyscale;
            case Gmagick::COLORSPACE_CMYK:
                return new PaletteCmyk;
            default:
                throw new \InvalidArgumentException('Unsupported color space.');
                break;
        }
    }
}
