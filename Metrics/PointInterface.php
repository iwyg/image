<?php

/*
 * This File is part of the Thapp\Image\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Metrics;

/**
 * @interface PointInterface
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PointInterface
{
    public function getX();
    public function getY();
    public function isIn(BoxInterface $box);
    public function negate();
}
