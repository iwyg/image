<?php

/**
 * This File is part of the Thapp\Image\Tests\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Cache;

use \Mockery as m;
use \Thapp\Image\Cache\HybridCache;

/**
 * @class HybridCacheTest
 * @package Thapp\Image\Tests\Cache
 * @version $Id$
 */
class HybridCacheTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $client = $this->getClient();

        $this->assertInstanceof(
            '\Thapp\Image\Cache\CacheInterface',
            new HybridCache(
                $client
            )
        );

        $client = $this->getClient();
    }

    /** @test */
    public function itShouldReturnCachedState()
    {
        $state = ['foo' =>
            [
                'p' => [
                    'someid' => $r = $this->getResource()
                ]
            ]
        ];

        $client = $this->getClient($state);

        $cache = new HybridCache(
            $client,
            'foo'
        );

        $this->assertTrue($cache->has('p.someid'));
    }

    protected function getResource()
    {
        return m::mock('\Thapp\Image\Resource\CachedResource');
    }

    protected function getClient($state = [])
    {
        $client = m::mock('\Thapp\Image\Cache\ClientInterface');
        $client->shouldReceive('has')->andReturnUsing(function ($id) use ($state) {
            return isset($state[$id]);
        });

        $client->shouldReceive('get')->andReturnUsing(function ($id) use ($state) {
            return isset($state[$id]) ? $state[$id] : null;
        });

        $client->shouldReceive('set');

        return $client;
    }

    protected function getFs()
    {
        return m::mock('\Symfony\Component\Filesystem\Filesystem');
    }

    protected function tearDown()
    {
        m::close();
    }
}
