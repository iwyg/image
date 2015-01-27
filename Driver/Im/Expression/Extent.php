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
use Thapp\Image\Metrics\PointInterface;

/**
 * @class Crop
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Extent implements ExpressionInterface
{
    private $box;
    private $point;
    private $flag;

    public function __construct(BoxInterface $box, PointInterface $point, $flag = '')
    {
        $this->box = $box;
        $this->point = $point;
        $this->flag  = $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('-extent %sx%s%s', $this->box->getWidth(), $this->box->getHeight(), $this->flag);
    }
}
