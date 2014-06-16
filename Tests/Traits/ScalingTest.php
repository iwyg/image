<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Traits;

use \Thapp\Image\Traits\Scaling;

/**
 * @class ScalingTest
 * @package Thapp\Image
 * @version $Id$
 */
class ScalingTest extends \PHPUnit_Framework_TestCase
{
    use Scaling;

    /**
     * @test
     * @dataProvider cropProvider
     */
    public function itShouldCalcCropVals($gravity, $expected)
    {
        $this->assertSame($expected, $this->getCropCoordinates(300, 300, 100, 100, $gravity));

    }

    /** @test */
    public function itShouldGetRatio()
    {
        $r = $this->ratio(100, 200);

        $this->assertSame(0.5, $r);
    }

    public function cropProvider()
    {
        return [
            [1, ['x' => 0, 'y' => 0]],
            [2, ['x' => 100, 'y' => 0]],
            [3, ['x' => 200, 'y' => 0]],
            [4, ['x' => 0, 'y' => 100]],
            [5, ['x' => 100, 'y' => 100]],
            [6, ['x' => 200, 'y' => 100]],
            [7, ['x' => 0, 'y' => 200]],
            [8, ['x' => 100, 'y' => 200]],
            [9, ['x' => 200, 'y' => 200]],
        ];
    }
}
