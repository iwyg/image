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

/**
 * @class IdentifyTest
 *
 * @package Thapp\Image\Tests\Driver\Im
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentifyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $id = new Identify;

        $id->identify($this->asset('pattern4c.jpg'));
    }

    protected function asset($file)
    {
        return dirname(__DIR__).'/../Fixures/'.$file;
    }
}
