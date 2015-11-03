<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @interface CmykInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CmykInterface
{
    /**
     * Get the cyan value.
     *
     * @return float
     */
    public function getCyan();

    /**
     * Get the magenta value.
     *
     * @return float
     */
    public function getMagenta();

    /**
     * Get the yellow value.
     *
     * @return float
     */
    public function getYellow();

    /**
     * Get the black value.
     *
     * @return float
     */
    public function getKey();
}
