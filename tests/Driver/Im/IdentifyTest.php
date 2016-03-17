<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Im package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Im;

use Thapp\Image\Driver\Im\Identify;
use Thapp\Image\Tests\TestHelperTrait;

/**
 * @class IdentifyTest
 *
 * @package Thapp\Image\Tests\Driver\Im
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentifyTest extends \PHPUnit_Framework_TestCase
{
    use TestHelperTrait;

    /** @test */
    public function itShouldIdentifyImageProps()
    {
        $id = new Identify;
        $info = $id->identify($this->asset('pattern4c.jpg'));

        $this->assertArrayHasKey('colorspace', $info);
        $this->assertArrayHasKey('file', $info);
    }

    protected function asset($file)
    {
        return dirname(__DIR__).'/../Fixures/'.$file;
    }

    protected function setUp()
    {
        $this->skipIfImagemagick();
    }
}
