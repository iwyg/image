<?php

/*
 * This File is part of the Thapp\Image\Tests\Info package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Info;

use Thapp\Image\Info\MetaData;

/**
 * @class MetaDataTest
 *
 * @package Thapp\Image\Tests\Info
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MetaDataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Info\MetaDataInterface', new MetaData);
    }

    /** @test */
    public function itShouldHaveArrayAccess()
    {
        $meta = new MetaData;

        $meta['foo'] = 'bar';

        $this->assertSame('bar', $meta['foo']);
        unset($meta['foo']);
        $this->assertFalse(isset($meta['foo']));
    }
}
