<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image;

/**
 * @interface SourceInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface SourceInterface
{
    public function setName($name);
    public function getName();

    public function setPath($path);
    public function getPath();

    public function setContents($contents);
    public function getContents();
}
