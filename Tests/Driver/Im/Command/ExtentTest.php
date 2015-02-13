<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Im\Command;

use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Driver\Im\Command\Extent;

/**
 * @class ExtentTest
 *
 * @package Thapp\Image\Tests\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ExtentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider valueProvider
     */
    public function itShouldCompileToString($w, $h, $x, $y, $expected)
    {
        $size = new Size($w, $h);
        $point = new Point($x, $y);

        $extent = new Extent($size, $point);

        $this->assertSame($expected, $extent->asString());
    }

    public function valueProvider()
    {
        return [
            [1000, 1000, 100, 100, '-extent 1000x1000+100+100'],
            [1000, 1000, -100, 100, '-extent 1000x1000-100+100'],
            [1000, 1000, -100, -100, '-extent 1000x1000-100-100'],
            [1000, 1000, 100, -100, '-extent 1000x1000+100-100'],
            [1000, 1000, 0, 0, '-extent 1000x1000'],
            [1000, 1000, 100, 0, '-extent 1000x1000+100+0'],
            [1000, 1000, 0, -100, '-extent 1000x1000+0-100'],
            [1000, 1000, 0, 100, '-extent 1000x1000+0+100'],
        ];
    }
}
