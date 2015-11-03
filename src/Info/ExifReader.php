<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Info;

/**
 * {@inheritdoc}
 */
class ExifReader extends AbstractReader
{
    /**
     * {@inheritdoc}
     */
    public function readFromFile($file)
    {
        return new MetaData($this->readExifData($file));
    }

    /**
     * {@inheritdoc}
     */
    public function readFromBlob($blob)
    {
        $head = mb_substr($blob, 0, 2, '8bit');
        if ('II' === $head || 'MM' === $head) {
            $mime = 'image/tiff';
        } else {
            $mime = 'image/jpeg';
        }

        $url = 'data://'.$mime.';base64,'.base64_encode($blob);

        return new MetaData($this->readExifData($url));
    }

    /**
     * readExifData
     *
     * @param mixed $file
     *
     * @return void
     */
    private function readExifData($url)
    {
        if (false === ($exif = @exif_read_data($url, "FILE", true, false))) {
            return [];
        }

        $file = $exif['FILE'];
        $data = [];

        foreach (['IFD0', 'EXIF'] as $info) {
            if (!isset($exif[$info])) {
                continue;
            }

            $prefix = strtolower($info);

            foreach ($exif[$info] as $key => $value) {
                $data[$prefix.'.'.$key] = $value;
            }
        }

        return $this->map($file + $data);
    }

    protected function getMappedKeys()
    {
        return [];
    }
}
