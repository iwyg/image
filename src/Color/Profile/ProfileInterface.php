<?php

/*
 * This File is part of the Thapp\Image package
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
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ProfileInterface
{
    /** @var int */
    const PROFILE_RGB = 0;

    /** @var int */
    const PROFILE_CMYK = 1;

    /** @var int */
    const PROFILE_GRAYSCALE = 2;

    /** @var string */
    const RESOURCE_PATH = __DIR__ . '/../../../resource';

    /**
     * Get the file path to the icc profile.
     *
     * @return string
     */
    public function getFile();

    /**
     * Get the name of the icc profile.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the file content of the icc profile.
     *
     * @return string
     */
    public function getContent();

    /**
     * Should call `self::getContent()`
     *
     * @see self#getContent()
     *
     * @return string
     */
    public function __toString();
}
