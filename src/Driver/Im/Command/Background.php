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

use Thapp\Image\Color\ColorInterface;

/**
 * @class Background
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Background extends AbstractCommand
{
    /**
     * color
     *
     * @var ColorInterface
     */
    private $color;

    /**
     * Constructor.
     *
     * @param ColorInterface $color
     */
    public function __construct(ColorInterface $color = null)
    {
        $this->color = $color;
    }

    /**
     * getColor
     *
     * @return ColorInterface
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-background %s', $this->color ? $this->color->getColorAsString() : 'none');
    }
}
