<?php

/**
 * This File is part of the Driver\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Loader;

use \Thapp\Image\Loader\RemoteLoader;

/**
 * @class RemoteLoaderTest
 * @package Driver\Loader
 * @version $Id$
 */
class RemoteLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Loader\LoaderInterface', new RemoteLoader);
    }

    /**
     * @test
     * @dataProvider urlProvider
     */
    public function itShouldSupportRemoteUrls($source, $supports)
    {
        $loader = new RemoteLoader;
        $this->assertSame($supports, $loader->supports($source));
    }

    /** @test */
    public function itShouldLoadRemoteFiles()
    {
        $loader = $this->loader = new RemoteLoader;
        $this->assertTrue(is_file($file = $loader->load('http://lorempixel.com/g/400/200/')));
        $this->assertTrue(is_file($loader->getSource()));
        $this->assertSame($file, $loader->getSource());
    }

    /** @test */
    public function itShouldNotLoadInvalidSources()
    {
        $loader = $this->loader = new RemoteLoader;
        $this->assertFalse($loader->load('http://example.com/doesnotexist.jpg'));
    }

    /**
     * urlProvider
     *
     * @access public
     * @return array
     */
    public function urlProvider()
    {
        return [
            ['http://lorempixel.com/g/400/200/', true],
            ['https://lorempixel.com/g/400/200/', true],
            ['spdy://lorempixel.com/g/400/200/', true],
            ['lorempixel.com/g/400/200/', false],
        ];
    }

    protected function tearDown()
    {
        if ($this->loader) {
            $this->loader->clean();
            $this->loader = null;
        }
    }
}
