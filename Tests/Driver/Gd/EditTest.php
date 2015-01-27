<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gd;

use Thapp\Image\Driver\Gd\Edit;
use Thapp\Image\Driver\Gd\Source;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
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
    /** @test */
    public function itShouldPreserveAlphaOnEdit()
    {
        $edit = $this->newEdit('transparent4.png');
        $this->assertTransparent($this->image, true);

        $edit = $this->newEdit('transparent4.png');
        $edit->rotate(45, $c = $this->image->getPalette()->getColor([255,255,255,0]));
        $this->assertTransparent($this->image, true);

        $edit = $this->newEdit('transparent4.png');
        $edit->canvas(new Box(400, 400), new Point(100, 100), $this->image->getPalette()->getColor([0,0,0,0]));
        $this->assertTransparent($this->image, true);
    }

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
        if (isset($_ENV['IMAGE_DRIVER']) && 'gd' !== $_ENV['IMAGE_DRIVER']) {
            $this->markTestIncomplete();
        }
    }

}
