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

use \Thapp\Image\Resource\CachedResource;

/**
 * @class CachedResourceTest
 * @package Thapp\Image
 * @version $Id$
 */
class CachedResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeImmutableAfterCreation()
    {
        $res = new CachedResource('/path', '', $t = time());

        $res->setLastModified(0);
        $res->setContents('bar');
        $res->setMimeType('image/jpeg');
        $res->setPath('/newpath');

        $this->assertSame('/path', $res->getPath());
        $this->assertSame('application/octet-stream', $res->getMimeType());
        $this->assertSame($t, $res->getLastModified());
        $this->assertSame('', $res->getContents());
    }
}
