<?php

/*
 * This File is part of the Thapp\Image\Color package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @class AbstractRgb
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractRgb implements RgbInterface
{
    protected $values;

    /**
     * getColorValuesAsArray
     *
     * @return array
     */
    protected function getColorValuesAsArray()
    {
        if (null === $this->values) {
            $r = $this->getRed();
            $g = $this->getGreen();
            $b = $this->getBlue();
            $a = $this->getAlpha();
            $this->values = compact('r', 'g', 'b', 'a');
        }

        return $this->values;
    }
}
