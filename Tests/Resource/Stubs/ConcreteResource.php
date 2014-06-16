<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Resource\Stubs;

use \Thapp\Image\Resource\AbstractResource;

/**
 * @class ConcreateResource
 * @package Thapp\Image
 * @version $Id$
 */
class ConcreteResource extends AbstractResource
{
    public function __construct($path = null, $contents = '', $lastModified = 0, $mime = 'application/octet-stream')
    {
        $this->path = $path;
        $this->contents = $contents;
        $this->lastModified = $lastModified;
        $this->mimeType = $mime;
    }
}
