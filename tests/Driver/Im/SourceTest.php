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

use Thapp\Image\Exception\ImageException;
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
    protected function setUp()
    {
        $this->skipIfImagemagick();
        parent::setUp();
    }
    /**
     * {@inheritdoc}
     */
    protected function getSourceClass()
    {
        return 'Thapp\Image\Driver\Im\Source';
    }

    /** @test */
    public function itShouldThrowOnLoadingIvalidFile()
    {
        $source = $this->newSource();

        try {
            $source->load('not a file');
        } catch (ImageException $e) {
            $this->assertSame('Cannot load image "not a file". No such file or directory', $e->getMessage());
            return;
        }

        $this->fail();
    }
}
