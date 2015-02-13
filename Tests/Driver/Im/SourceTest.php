<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Im package
 *
 * (c)  <>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Im;

use Thapp\Image\Tests\Driver\SourceTest as AbstracSourceTest;

/**
 * @class SourceTest
 *
 * @package Thapp\Image\Tests\Driver\Im
 * @version $Id$
 * @author  <>
 */
class SourceTest extends AbstracSourceTest
{
    /**
     * {@inheritdoc}
     */
    protected function getSourceClass()
    {
        return 'Thapp\Image\Driver\Im\Source';
    }
}
