<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Imagick;

use Imagick;
use Thapp\Image\Driver\Imagick\Edit;
use Thapp\Image\Driver\Imagick\Source;
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
    /** @test */
    public function itShouldPreserveAlpha()
    {
        $edit = $this->newEdit('transparent4.png');
        $this->assertAlphaChannelImagick($this->image, Imagick::ALPHACHANNEL_ACTIVATE);

        $edit = $this->newEdit('transparent4.png');
        $edit->rotate(45, $c = $this->image->getPalette()->getColor([255,255,255,0]));
        $this->assertAlphaChannelImagick($this->image, Imagick::ALPHACHANNEL_ACTIVATE);

        $edit = $this->newEdit('transparent4.png');
        $edit->canvas(new Size(400, 400), new Point(100, 100), $this->image->getPalette()->getColor([0,0,0,0.2]));
        $this->assertAlphaChannelImagick($this->image, Imagick::ALPHACHANNEL_ACTIVATE);

        $edit = $this->newEdit('transparent4.png');
        $this->image->setGravity(new Gravity(1));
        $edit->crop(new Size(100, 100));
        $this->assertAlphaChannelImagick($this->image, Imagick::ALPHACHANNEL_ACTIVATE);
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
        if (!class_exists('Imagick') || (isset($_ENV['IMAGE_DRIVER']) && 'imagick' !== $_ENV['IMAGE_DRIVER'])) {
            $this->markTestIncomplete();
        }

        parent::setUp();
    }
}
