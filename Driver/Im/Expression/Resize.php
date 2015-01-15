<?php

/*
 * This File is part of the Lucid\Image\Driver\Im\Expression package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Expression;

use Thapp\Image\Metrics\BoxInterface;

/**
 * @class Resize
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Resize implements ExpressionInterface
{
    private $box;

    public function __construct(BoxInterface $box)
    {
        $this->box = $box;
    }

    public function __toString()
    {
        return sprintf('-resize %sx%s!', $this->box->getWidth(), $this->box->getHeight());
    }
}
