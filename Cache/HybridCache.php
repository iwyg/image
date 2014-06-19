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

use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Resource\CachedResource;
use \Symfony\Component\Filesystem\Filesystem;

/**
 * @class HybridCache extends AbstractCache
 * @see AbstractCache
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class HybridCache extends FilesystemCache
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var boolean
     */
    private $changes;

    /**
     * @var boolean
     */

    /**
     * Crate a new HybridCache
     *
     * @param ClientInterface $client the cache client
     * @param Filesystem $fs          the the filesystem
     * @param string $id              meta info id
     * @param string $path            image storage path
     * @param string $pfx             image name prefix
     *
     */
    public function __construct(ClientInterface $client, $id = 'hbrd', $path = null, $pfx = null)
    {
        parent::__construct($path, $pfx);

        $this->id      = $id;
        $this->client  = $client;
        $this->changes = false;

        $this->initialize();
    }

    /**
     * pass changes from the pool to the client
     */
    public function __destruct()
    {
        if ($this->changes) {
            $this->client->set($this->id, $this->pool);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        list ($prefix, $id) = explode('.', $key);

        return isset($this->pool[$prefix][$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $raw = self::CONTENT_RESOURCE)
    {
        if (!$this->has($key)) {
            return;
        }

        list ($prefix, $id) = explode('.', $key);

        return $raw ? $this->pool[$prefix][$id]->getContents() : $this->pool[$prefix][$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, ProcessorInterface $proc)
    {
        $this->changes = true;

        list ($prefix, $id) = explode('.', $key);

        $this->pool[$prefix][$id] = $this->createResource($proc, $key, $prefix, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $this->changes = true;

        parent::purge();

        unset($this->pool);

        $this->pool = [];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($file)
    {
        $this->changes = true;

        parent::delete($file);

        list($prefix, $id) = $this->createKey($file);

        unset($this->pool[$prefix]);
    }

    /**
     * createResource
     *
     * @param ProcessorInterface $proc
     * @param string $key
     * @param string $prefix
     * @param string $id
     *
     * @return ResourceInterface an instance of \Thapp\Image\Resource\CachedResource
     */
    private function createResource(ProcessorInterface $proc, $key, $prefix, $id)
    {
        $resource = new CachedResource($proc, $file = $this->getImagePath($prefix, $id) . '.' . $proc->getFileFormat());

        $this->dumpFile($file, $proc->getContents());

        return $resource;
    }

    /**
     * getImagePath
     *
     * @param string $prefix
     * @param string $id
     *
     * @return string
     */
    private function getImagePath($prefix, $id)
    {
        return $this->path . DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR . $id;
    }

    /**
     * Initialize the pool data.
     *
     * @return void
     */
    private function initialize()
    {
        $this->pool = $this->client->has($this->id) ? $this->client->get($this->id) : [];
    }
}
