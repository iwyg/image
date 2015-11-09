<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use RuntimeException;
use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Driver\AbstractSource;
use Thapp\Image\Exception\ImageException;

/**
 * @class Source
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source extends AbstractSource
{
    use GdHelper;

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        try {
            list ($gd, $mime) = $this->gdCreateFromFile($file);
        } catch (\Exception $e) {
            throw ImageException::load($e);
        }

        $image = new Image($gd, new Rgb, $this->getReader()->readFromFile($file));
        $image->setSourceFormat($this->getImageTypeFromMimetype($mime));

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function create($content)
    {
        try {
            list($gd, $mime) = $this->gdCreateFromBlob($content);
        } catch (\Exception $e) {
            throw ImageException::create($e);
        }

        $image = new Image($gd, new Rgb, $this->getReader()->readFromBlob($content));
        $image->setSourceFormat($this->getImageTypeFromMimetype($mime));

        return $image;
    }

    /**
     * post process GD resource creation
     *
     * @param resource GD resource $gd
     *
     * @return void
     */
    private function postProcess(&$gd)
    {
        if (!is_resource($gd) || !$this->ensureTrueColor($gd)) {
            throw new ImageException('Can\'t prepare image.');
        }

        imagealphablending($gd, false);
        imagesavealpha($gd, true);
    }

    /**
     * Ensures GD resource has TrueColor
     *
     * @param resource $gd
     *
     * @return bool
     */
    private function ensureTrueColor(&$gd)
    {
        if (imageistruecolor($gd)) {
            return true;
        }

        return imagepalettetotruecolor($gd);
    }

    /**
     * Get the MimeType from file path.
     *
     * @param string $file
     *
     * @return string
     */
    private function getMimeFromFile($file)
    {
        $handle = fopen($file, 'r');
        $mime = $this->getMimeFromBlob(fread($handle, 8));
        fclose($handle);

        return $mime;
    }

    /**
     * Get the MimeType from blob.
     *
     * @param string $blob
     *
     * @return string
     */
    private function getMimeFromBlob($blob)
    {
        $info = finfo_open(FILEINFO_MIME);

        list($mime,) = explode(';', finfo_buffer($info, $blob), 2);
        finfo_close($info);

        if (0 !== strpos($mime, 'image/')) {
            return false;
        }

        return $mime;
    }

    /**
     * Creates a GD resource from a blob.
     *
     * @param string $blob
     *
     * @return resource GD resource
     */
    private function gdCreateFromBlob(&$blob)
    {
        if (!$mime = $this->getMimeFromBlob(mb_substr($blob, 0, 8, '8bit'))) {
            throw new RuntimeException('Cannot detect image type.');
        }

        if (!$gd = imagecreatefromstring($blob)) {
            throw new RuntimeException;
        }

        $this->postProcess($gd);

        return [$gd, $mime];
    }

    /**
     * Creates an GD resource from a given filepath.
     *
     * @param string $file
     * @param string $mime
     *
     * @return resource GD resource
     */
    private function gdCreateFromFile($file)
    {
        if (!$mime = $this->getMimeFromFile($file)) {
            throw new RuntimeException(sprintf('Cannot detect image type for %s.', $file));
        }


        if (!function_exists($fn = $this->getCreateFunc($mime))) {
            throw new RuntimeException(sprintf('Unsupported image type in  %s.', $file));
        }

        if (!$gd = call_user_func($fn, $file)) {
            throw new RuntimeException(sprintf('Createing GD resource failed for %s.', $file));
        }

        $this->postProcess($gd);

        return [$gd, $mime];
    }

    /**
     * Get the gd creation function name.
     *
     * @param string $mime
     *
     * @return string
     */
    private function getCreateFunc($mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return 'imagecreatefromjpeg';
            case 'image/png':
                return 'imagecreatefrompng';
            case 'image/gif':
                return 'imagecreatefromgif';
            case 'image/vnd.wap.wbmp':
                return 'imagecreatefromwbmp';
            case 'image/webp':
                return 'imagecreatefromwebp';
            case 'image/x-xbitmap':
            case 'image/x-xbm':
                return 'imagecreatefromxbm';
            default:
                break;
        }
    }

    /**
     * Get an image type representation of a given file mimetype.
     *
     * @param string $mime
     *
     * @return string
     */
    private function getImageTypeFromMimetype($mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return ImageInterface::FORMAT_JPEG;
            case 'image/png':
                return ImageInterface::FORMAT_PNG;
            case 'image/gif':
                return ImageInterface::FORMAT_GIF;
            case 'image/vnd.wap.wbmp':
                return ImageInterface::FORMAT_WBMP;
            case 'image/webp':
                return ImageInterface::FORMAT_WEBP;
            case 'image/x-xbitmap':
            case 'image/x-xbm':
                return ImageInterface::FORMAT_XBM;
            default:
                return null;
        }
    }
}
