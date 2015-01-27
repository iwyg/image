<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class EditTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class EditTest extends \PHPUnit_Framework_TestCase
{
    use ImageTestHelper;

    protected $image;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Driver\EditInterface', $this->newEdit([]));
    }

    /** @test */
    public function itShouldCropImage()
    {
        $edit = $this->newEdit([200, 100]);
        $edit->crop(new Box(50, 50));

        $this->assertSame(50, $this->image->getHeight());
        $this->assertSame(50, $this->image->getWidth());
    }

    /** @test */
    public function itShouldRotateImage()
    {
        $edit = $this->newEdit([200, 100]);
        $edit->rotate(90);

        $this->assertSame(100, $this->image->getWidth());
        $this->assertSame(200, $this->image->getHeight());

        $edit->rotate(180);

        $this->assertSame(100, $this->image->getWidth());
        $this->assertSame(200, $this->image->getHeight());

        $edit->rotate(360);

        $this->assertSame(100, $this->image->getWidth());
        $this->assertSame(200, $this->image->getHeight());
    }

    /** @test */
    public function itShouldPaseteImage()
    {
        $edit = $this->newEdit([200, 100]);
        $img = $this->newImage([100, 50]);

        //$edit->paste($img, new Point(0, 0));
    }

    /** @test */
    public function itShouldResizeImage()
    {
        $edit = $this->newEdit([200, 100]);
        $new = new Box(400, 400);
        $edit->resize($new);

        $size = $this->image->getSize();

        $this->assertSame($size->getWidth(), $new->getWidth());
        $this->assertSame($size->getHeight(), $new->getHeight());
    }

    /** @test */
    public function itPastesImages()
    {
        $edit = $this->newEdit([600, 600]);

        //if ($edit instanceof \Thapp\Image\Driver\Gd\Edit) {
        //    var_dump('skipping test');
        //    $this->markTestIncomplete();
        //}

        $image = $this->newImage('animated.gif');
        $prefix = strtr(get_class($edit), ['\\' => '_']);

        $this->image->setGravity(new Gravity(5));
        $edit->paste($image);

        $this->image->save($this->asset($prefix.'_pasted.gif'), 'gif');
    }

    abstract protected function newEdit($file, ImageInterface $image = null);

    abstract protected function newSource();

    protected function newImage($file = null)
    {
        $source = $this->newSource();

        if (is_array($file)) {
            list ($w, $h, $format) = $this->mergeFileArgs($file);

            return $source->read($this->getTestImage($w, $h, $format));
        }

        return $source->load($this->asset($file));
    }

    protected function mergeFileArgs(array $file)
    {
        foreach ([400, 400, 'jpeg'] as $i => $val) {
            if (!isset($file[$i])) {
                $file[$i] = $val;
            }
        }

        return $file;
    }

    protected function mockImage()
    {
        return $this->getMockBuilder('Thapp\Image\Driver\ImageInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
