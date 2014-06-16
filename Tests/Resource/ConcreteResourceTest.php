<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Resource;

use \Thapp\Image\Tests\Resource\Stubs\ConcreteResource;

/**
 * @class CachedResourceTest
 * @package Thapp\Image
 * @version $Id$
 */
class ConcreteResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeMutableAfterCreation()
    {
        $res = new ConcreteResource('/path', '', $t = time());

        $this->assertSame('/path', $res->getPath());
        $this->assertSame('application/octet-stream', $res->getMimeType());
        $this->assertSame($t, $res->getLastModified());
        $this->assertSame('', $res->getContents());

        $res->setLastModified(0);
        $res->setContents('bar');
        $res->setMimeType('image/jpeg');
        $res->setPath('/newpath');

        $this->assertSame('/newpath', $res->getPath());
        $this->assertSame('image/jpeg', $res->getMimeType());
        $this->assertSame(0, $res->getLastModified());
        $this->assertSame('bar', $res->getContents());
    }
}
