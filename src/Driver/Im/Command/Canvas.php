<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Color\ColorInterface;

/**
 * @class Canvas
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Canvas extends AbstractCommand
{
    /** @var ColorInterface */
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
