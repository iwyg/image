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

use Gmagick;
use Thapp\Image\Driver\Gmagick\Edit;
use Thapp\Image\Driver\Gmagick\Source;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Tests\Driver\EditTest as AbstractEditTest;

/**
 * @class EditTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EditTest extends AbstractEditTest
{
    protected function newEdit($file, ImageInterface $image = null)
    {
        if (null !== $this->image) {
            //$this->image->destroy();
            //$this->image = null;
        }

        return new Edit($this->image = $image ?: $this->newImage($file));
    }

    protected function newSource()
    {
        return new Source;
    }

    protected function setUp()
    {
        $this->skipIfGmagick();

        parent::setUp();
    }
}
