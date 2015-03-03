<?php

/*
 * This File is part of the Thapp\Image\Tests\Color\Profile package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Color\Profile;

use Thapp\Image\Color\Profile\Profile;

/**
 * @class ProfileTest
 *
 * @package Thapp\Image\Tests\Color\Profile
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Thapp\Image\Color\Profile\ProfileInterface',
            new Profile('icc', $this->getProfile())
        );
    }

    /** @test */
    public function itShouldCreateProfileFromString()
    {
        $this->assertInstanceof(
            'Thapp\Image\Color\Profile\ProfileInterface',
            Profile::fromString('icc', file_get_contents($this->getProfile()))
        );
    }

    /** @test */
    public function itShouldGetItsName()
    {
        $this->assertSame('icc', (new Profile('icc', $this->getProfile()))->getName());
    }

    /** @test */
    public function itShouldGetProfileContent()
    {
        $this->assertStringEqualsFile($this->getProfile(), (string)(new Profile('icc', $this->getProfile())));
    }

    /** @test */
    public function itShouldThrowOnInvalidContent()
    {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        $path = $meta['uri'];
        $profile = new Profile('icc', $path);
        fclose($tmp);

        try {
            $profile->getContent();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function itShouldThrowOnInvalidPath()
    {
        try {
            $profile = new Profile('icc', '/some/profile');
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function itShouldThrowOnInvalidContentType()
    {
        try {
            $profile = new Profile('icc', []);
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }

    protected function getProfile()
    {
        return __DIR__.'/../../../resource/color.org/sRGB_IEC61966-2-1_black_scaled.icc';
    }
}
