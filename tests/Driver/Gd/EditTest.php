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
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
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
    public function itShouldThrowIfPasteImageIsInvalid()
    {
        $edit = $this->newEdit([200, 200]);
        try {
            $edit->paste($this->mockImage('Thapp\Image\Driver\ImageInterface'));
        } catch (\LogicException $e) {
            $this->assertSame('Can\'t copy image from different driver.', $e->getMessage());
        }
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

    protected function trearDown()
    {
        foreach ($this->images as $image) {
            $image->destroy();
        }

        if (null !== $this->image) {
            $image->destroy();
        }
    }
}