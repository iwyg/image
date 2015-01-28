<?php

/*
 * This File is part of the Thapp\Image\Tests\Stubs\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Stubs\Filter;

use Thapp\Image\Driver\Gmagick\Image;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\Gmagick\GmagickFilter as Filter;

/**
 * @class FilterStub
 *
 * @package Thapp\Image\Tests\Stubs\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class GmagickFilter extends Filter
{
    public function apply(ImageInterface $image)
    {
    }
}
