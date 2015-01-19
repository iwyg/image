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
        rewind($resource);

        return $this->create(stream_get_contents($resource));

    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        if (!is_file($file)) {
            return false;
        }

        if (!$gd = Image::gdCreateFromFile($file)) {
            return false;
        }

        $image = new Image($gd);
        $info = getimagesize($file);
        $image->setFormat($this->getImageTypeFromMimetype($info['mime']));

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function create($content)
    {
        $image = new Image(imagecreatefromString($content));

        if ($type = $this->getImageTypeFromString(mb_substr($content, 0, 16, '8bit'))) {
            $image->setFormat($type);
        }

        return $image;
    }

    protected function getImageTypeFromString($image)
    {
        list($mime, ) = explode(';', finfo_buffer($info = finfo_open(FILEINFO_MIME), $image));
        finfo_close($info);

        return $this->getImageTypeFromMimetype($mime);
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
