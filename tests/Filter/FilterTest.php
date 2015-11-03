<?php

/*
 * This File is part of the Thapp\Image\Tests\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Filter;

use Thapp\Image\Tests\TestHelperTrait;
use Thapp\Image\Tests\Stubs\Filter\GdFilter;
use Thapp\Image\Tests\Stubs\Filter\ImagickFilter;
use Thapp\Image\Tests\Stubs\Filter\GmagickFilter;

/**
 * @class FilterTest
 *
 * @package Thapp\Image\Tests\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    use TestHelperTrait;

    /** @test */
    public function itShouldSupportDriverTypesForImagick()
    {
        $this->skipIfImagick();

        $filterIm = new ImagickFilter();
        $filterGd = new GdFilter();


        $gd = $this->getMockBuilder('Thapp\Image\Driver\Gd\Image')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertTrue($filterGd->supports($gd));

        $im = $this->getMockBuilder('Thapp\Image\Driver\Imagick\Image')
            ->setMethods(['filter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($filterIm->supports($im));
        $this->assertFalse($filterIm->supports($gd));

        $this->assertFalse($filterGd->supports($im));
    }

    /** @test */
    public function itShouldSupportDriverTypesForGmagick()
    {
        $this->skipIfGmagick();

        $filterIm = new GmagickFilter();
        $filterGd = new GdFilter();

        $gd = $this->getMockBuilder('Thapp\Image\Driver\Gd\Image')
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertTrue($filterGd->supports($gd));

        $im = $this->getMockBuilder('Thapp\Image\Driver\Gmagick\Image')
            ->setMethods(['filter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($filterIm->supports($im));
        $this->assertFalse($filterIm->supports($gd));

        $this->assertFalse($filterGd->supports($im));
    }
}
