<?php

/*
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests;

/**
 * @trait TestHelperTrait
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait TestHelperTrait
{
    protected function skipIfGmagick($message = 'Gmagick extension not installed.')
    {
        if (!extension_loaded('gmagick') || (isset($_ENV['IMAGE_DRIVER']) && 'gmagick' !== $_ENV['IMAGE_DRIVER'])) {
            $this->markTestSkipped($message);
        }
    }

    protected function skipIfImagick($message = 'Imagick extension not installed.')
    {
        if (!extension_loaded('imagick') || (isset($_ENV['IMAGE_DRIVER']) && 'imagick' !== $_ENV['IMAGE_DRIVER'])) {
            $this->markTestSkipped($message);
        }
    }

    protected function skipIfImagemagick($message = 'imagemagick not available.')
    {
        ob_start();
        $im = system('which convert');
        ob_end_clean();

        //if ('' === trim($im) || (!isset($_ENV['IMAGE_DRIVER']) || 'im' !== $_ENV['IMAGE_DRIVER'])) {
        if ('' === trim($im)) {
            $this->markTestSkipped($message);
        }
    }

    protected function fixure($path = null)
    {
        $fixure = __DIR__ . '/Fixures';

        if (null === $path) {
            return $fixure;
        }

        return $fixure . '/' . trim($path, '/');
    }
}
