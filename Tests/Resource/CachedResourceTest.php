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

use \Mockery as m;
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
        $proc = $this->getProcMock('', 'application/octet-stream', $t = time());
        $res = new CachedResource($proc, '/path');


        $res->setLastModified(0);
        $res->setContents('bar');
        $res->setMimeType('image/jpeg');
        $res->setPath('/newpath');

        $this->assertSame('/path', $res->getPath());
        $this->assertSame('application/octet-stream', $res->getMimeType());
        $this->assertSame($t, $res->getLastModified());
        $this->assertSame('', $res->getContents());
    }

    protected function getProcMock($content = null, $mime = null, $time = null)
    {
        $proc = m::mock('Thapp\Image\ProcessorInterface');

        $proc->shouldReceive('getContents')->andReturn($content);
        $proc->shouldReceive('getMimeType')->andReturn($mime);
        $proc->shouldReceive('getLastModTime')->andReturn($time);
        $proc->shouldReceive('getTargetSize')->andReturn(['w' => 100, 'h' => 100]);

        return $proc;
    }

    protected function tearDown()
    {
        m::close();
    }
}
