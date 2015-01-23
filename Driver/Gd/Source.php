<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use Thapp\Image\Driver\SourceInterface;
use Thapp\Image\Exception\SourceException;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source implements SourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource)) {
            throw SourceException::resource();
        }

        try {
            return $this->create(stream_get_contents($resource));
        } catch (SourceException $e) {
            throw SourceException::read($e->getPrevious());
        }

    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        if (!is_file($file)) {
            return false;
        }

        $info = getimagesize($file);

        if (!$gd = $this->postProcess($this->gdCreateFromFile($file, $info['mime']))) {
            throw SourceException::load();
        }

        $image = new Image($gd);
        $image->setSourceFormat($this->getImageTypeFromMimetype($info['mime']));

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function create($content)
    {
        $image = new Image($this->postProcess(imagecreatefromString($content)));
        $meta = getimagesizefromstring($content);

        if (isset($meta['mime'])) {
            $image->setSourceFormat($this->getImageTypeFromMimetype($meta['mime']));
        }

        return $image;
    }

    protected function postProcess($gd)
    {
        if (!imageistruecolor($gd)) {
            /*$resource = $gd;*/
            /*$gd = imagepalettetotruecolor($resource);*/
            /*imagedestroy($resource);*/
        }

        imagealphablending($gd, false);
        imagesavealpha($gd, true);

        return $gd;
    }

    protected function gdCreateFromFile($file, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file);
            case 'image/png':
                return imagecreatefrompng($file);
            case 'image/gif':
                return imagecreatefromgif($file);
            case 'image/vnd.wap.wbmp':
                return imagecreatefromwbmp($file);
            case 'image/webp':
                return imagecreatefromwebp($file);
            case 'image/x-xbitmap':
            case 'image/x-xbm':
                return imagecreatefromxbm($file);
            default:
                return false;
        }
    }

    protected function getImageTypeFromMimetype($mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return 'jpeg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            case 'image/vnd.wap.wbmp':
                return 'wbmp';
            case 'image/webp':
                return 'webp';
            case 'image/x-xbitmap':
            case 'image/x-xbm':
                return 'xbm';
            default:
                return null;
        }
    }
}
