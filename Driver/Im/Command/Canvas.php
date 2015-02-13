<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c)  <>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Color\ColorInterface;

/**
 * @class Canvas
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author  <>
 */
class Canvas extends AbstractCommand
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
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('xc:%s', null !== $this->color ? $this->color->getColorAsString() : 'none');
    }
}
