<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Geometry;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Gravity;

/**
 * @class GravityTest
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class GravityTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Geometry\GravityInterface', new Gravity(5));
    }

    /** @test */
    public function itShouldGetMode()
    {
        $g = new Gravity(5);
        $this->assertSame(5, $g->getMode());

        $g = new Gravity(0);
        $this->assertSame(1, $g->getMode());

        $g = new Gravity(10);
        $this->assertSame(9, $g->getMode());
    }

    /**
     * @test
     * @dataProvider gravityProvider
     */
    public function itShouldCalculateCropPoints($mode, $ex, $ey)
    {
        $box = new Size(300, 300);
        $target = new Size(100, 100);
        $gravity = new Gravity($mode);

        $point = $gravity->getPoint($box, $target);

        $this->assertSame($ex, $point->getX());
        $this->assertSame($ey, $point->getY());
    }

    public function gravityProvider()
    {
        return [
            [1, 0, 0],
            [2, 100, 0],
            [3, 200, 0],
            [4, 0, 100],
            [5, 100, 100],
            [6, 200, 100],
            [7, 0, 200],
            [8, 100, 200],
            [9, 200, 200],
        ];
    }
}
