<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Color\Palette\PaletteInterface;

/**
 * @class Colorspace
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Colorspace extends AbstractCommand
{
    /**
     * palette
     *
     * @var PaletteInterface
     */
    private $palette;

    public function __construct(PaletteInterface $palette)
    {
        $this->palette = $palette;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        $profile = new Profile($this->palette->getProfile());

        return sprintf('%s -colorspace %s', $profile->asString(), $this->translateColorspace($this->palette));
    }

    /**
     * translateColorspace
     *
     * @param PaletteInterface $palette
     *
     * @return string
     */
    private function translateColorspace(PaletteInterface $palette)
    {
        switch($palette->getConstant()) {
            case PaletteInterface::PALETTE_RGB:
                return 'RGB';
            case PaletteInterface::PALETTE_CMYK:
                return 'CMYK';
            case PaletteInterface::PALETTE_GRAYSCALE:
                return 'GRAY';
            default:
                break;
        }

        throw new \InvalidArgumentException('Colorspace is not supported.');
    }
}
