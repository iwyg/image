<?php

/*
 * This File is part of the Thapp\Image\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Grayscale;

/**
 * @class Flip
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Palette implements FilterInterface
{
    use FilterHelperTrait;

    const PALETTE_RGB = 'rgb';
    const PALETTE_CMYK = 'cmyk';
    const PALETTE_GRAY = 'gray';

    private $mode;

    public function __construct($mode = self::PALETTE_RGB)
    {
        $this->mode = $mode;
    }

    protected function applyFilter(ImageInterface $image)
    {
        switch ($this->mode) {
            case self::PALETTE_RGB:
                $palette = new Rgb;
                break;
            case self::PALETTE_CMYK:
                $palette = new Cmyk;
                break;
            case self::PALETTE_GRAY:
                $palette = new Grayscale;
                break;
            default:
                throw new InvalidArgumentException();
                break;
        }

        $image->applyPalette($palette);
    }
}
