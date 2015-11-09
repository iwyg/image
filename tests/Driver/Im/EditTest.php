<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Im;

use Thapp\Image\Driver\Im\Edit;
use Thapp\Image\Driver\Im\Source;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Tests\Driver\EditTest as AbstractEditTest;

/**
 * @class EditTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class EditTest extends AbstractEditTest
{
    protected function newEdit($file, ImageInterface $image = null)
    {
        return new Edit($this->image = $image ?: $this->newImage($file));
    }

    protected function newSource()
    {
        return new Source;
    }

    protected function setUp()
    {
        $this->skipIfImagemagick();

        parent::setUp();
    }
}
