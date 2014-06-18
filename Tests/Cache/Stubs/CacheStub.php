<?php

/**
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Cache\Stubs;

use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Cache\AbstractCache;

/**
 * @class CacheStub
 * @package Thapp\Image\Tests
 * @version $Id$
 */
class CacheStub extends AbstractCache
{
    protected $pool = ['foo' => 'bar'];

    public function get($key, $raw = self::CONTENT_RESOURCE)
    {
    }

    public function set($key, ProcessorInterface $proc)
    {
    }

    public function purge()
    {
    }

    public function delete($image)
    {
    }
}
