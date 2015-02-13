<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Im\Command;

use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Grayscale;
use Thapp\Image\Driver\Im\Command\Colorspace;

/**
 * @class ExtentTest
 *
 * @package Thapp\Image\Tests\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ColorspaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldCompileToString()
    {
        $cs = new Colorspace(new Cmyk);

        var_dump($cs->asString());
    }
}
