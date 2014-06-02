<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Adapter;

use \League\Flysystem\FilesystemInterface;
use \Thapp\Image\Writer\WriterInterface;

/**
 * @class FlysystemWriter implements WriterInterface
 * @see WriterInterface
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FlysystemWriter implements WriterInterface
{
    /**
     * fs
     *
     * @var FilesystemInterface
     */
    protected $fs;

    /**
     * @param FilesystemInterface $fs
     */
    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
    }
    /**
     * write
     *
     * @param mixed $target
     *
     * @access public
     * @return mixed
     */
    public function write($target, $data = null)
    {
        $this->fs->put($target, $data);
    }
}
