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
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Profile\Profile;
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
        $this->validateStream($resource);

        $imagick = $this->newImagick();

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
        $imagick = $this->newImagick();

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
    public function create($content)
    {
        $imagick = $this->newImagick();

        try {
            $imagick->readImageBlob($content);
        } catch (ImagickException $e) {
            $imagick->destroy();

            throw ImageException::create($e);
        }

        return new Image($imagick, $this->getColorPalette($imagick), $this->getReader()->readFromBlob($content));
    }

    /**
     * newImagick
     *
     * @return \Imagick
     */
    private function newImagick()
    {
        $imagick = new Imagick;
        //$imagick->setBackgroundColor(new ImagickPixel('none'));

        return $imagick;
    }

    /**
     * getColorPalette
     *
     * @param Imagick $imagick
     *
     * @return \Thapp\Image\Color\PaletteInterface
     */
    private function getColorPalette(Imagick $imagick)
    {
        switch ($imagick->getImageColorspace()) {
            case Imagick::COLORSPACE_RGB:
            case Imagick::COLORSPACE_SRGB:
                return new Rgb($this->getIccProfile($imagick));
            case Imagick::COLORSPACE_CMYK:
                return new Cmyk($this->getIccProfile($imagick));
            case Imagick::COLORSPACE_GRAY:
                return new Grayscale($this->getIccProfile($imagick));
            default:
                break;
        }

        throw new ImageException('Unsupported color space.');
    }

    /**
     * getIccProfile
     *
     * @param Imagick $imagick
     *
     * @return \Thapp\Image\Color\Profile\ProfileInterface
     */
    private function getIccProfile(Imagick $imagick)
    {
        try {
            $profile = $imagick->getImageProfile('icc');
        } catch (ImagickException $e) {
            return;
        }

        return Profile::fromString('icc', $profile);
    }
}
