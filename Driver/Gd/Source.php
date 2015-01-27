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

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Driver\AbstractSource;
use Thapp\Image\Exception\ImageException;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source extends AbstractSource
{
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        if (!is_resource($resource) && 'stream' === get_resource_type($resource)) {
            throw ImageException::resource();
        }

        $meta = stream_get_meta_data($resource);

        try {
            if (isset($meta['uri']) && stream_is_local($meta['uri'])) {
                return $this->load($meta['uri']);
            }
            return $this->create(stream_get_contents($resource));
        } catch (ImageException $e) {
            throw ImageException::read($e);
        }

    }

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
     * postProcess
     *
     * @param resource GD resource $gd
     *
     * @return void
     */
    protected function postProcess(&$gd)
    {
        if (!is_resource($gd) || (!imageistruecolor($gd) && false === @imagepalettetotruecolor($gd))) {
            throw new ImageException('Can\'t prepare image.');
        }

        imagealphablending($gd, false);
        imagesavealpha($gd, true);
    }

    /**
     * getMimeFromFile
     *
     * @param string $file
     *
     * @return string
     */
    protected function getMimeFromFile($file)
    {
        $handle = fopen($file, 'r');
        $mime = $this->getMimeFromBlob(fread($handle, 8));
        fclose($handle);

        return $mime;
    }

    /**
     * getMimeFromBlob
     *
     * @param string $blob
     *
     * @return string
     */
    protected function getMimeFromBlob($blob)
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
     * gdCreateFromBlob
     *
     * @param string $blob
     *
     * @return resource GD resource
     */
    protected function gdCreateFromBlob(&$blob)
    {
        if (!$mime = $this->getMimeFromBlob(mb_substr($blob, 0, 8, '8bit'))) {
            throw new \RuntimeException('Cannot detect image type.');
        }

        if (!$gd = imagecreatefromstring($blob)) {
            throw new \RuntimeException;
        }

        $this->postProcess($gd);

        return [$gd, $mime];
    }

    /**
     * gdCreateFromFile
     *
     * @param string $file
     * @param string $mime
     *
     * @return resource GD resource
     */
    protected function gdCreateFromFile($file)
    {
        if (!$mime = $this->getMimeFromFile($file)) {
            throw new \RuntimeException(sprintf('Cannot detect image type for %s.', $file));
        }

        if (!function_exists($fn = $this->getCreateFunc($mime))) {
            throw new \RuntimeException(sprintf('Unsupported image type in  %s.', $file));
        }

        if (!$gd = call_user_func($fn, $file)) {
            throw new \RuntimeException(sprintf('Createing GD resource failed for %s.', $file));
        }

        $this->postProcess($gd);

        return [$gd, $mime];
    }

    protected function getCreateFunc($mime)
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
    protected function getImageTypeFromMimetype($mime)
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
