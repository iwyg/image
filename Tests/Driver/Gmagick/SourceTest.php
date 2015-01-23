<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gmagick;

use Thapp\Image\Tests\SourceTest as Source;

/**
 * @class SourceTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SourceTest extends Source
{
    protected function getSourceClass()
    {
        return 'Thapp\Image\Driver\Gmagick\Source';
    }

    protected function setUp()
    {
        if (!class_exists('Gmagick')) {
            $this->markTestIncomplete();
        }

        foreach ($this->images as $image) {
            $image->destroy();
        }

        $this->images = [];
    }
}
