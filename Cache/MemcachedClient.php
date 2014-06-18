<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Cache;

class MemcachedClient implements ClientInterface
{
    private $client;

    public function __construct(\Memcached $client)
    {
        $this->client = $client;
    }

    public function getDriver()
    {
        return $this->client;
    }

    public function set($id, $content)
    {
        $this->client->set($id, $content, 0);
    }

    public function get($id)
    {
        return $this->client->get($id);
    }

    public function has($id)
    {
        return (bool)$this->client->get($id);
    }

    public function delete($id)
    {
        $this->client->delete($id);
    }
}
