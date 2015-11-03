<?php

/*
 * This File is part of the Thapp\Image\Color\Profile package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color\Profile;

/**
 * @interface ProfileInterface
 *
 * @package Thapp\Image\Color\Profile
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ProfileInterface
{
    const PROFILE_RGB = 0;
    const PROFILE_CMYK = 1;
    const PROFILE_GRAYSCALE = 2;

    public function getFile();
    public function getName();
    public function getContent();

    public function __toString();
}
