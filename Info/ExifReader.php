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

        /*$info = getimagesizefromstring($blob);*/
        $url = 'data://'.$mime.';base64,'.base64_encode($blob);

        return new MetaData($this->readExifData($url));
    }

    /**
     * {@inheritdoc}
     */
    public function readFromStream($resource)
    {
        if (false === ($data = @stream_get_meta_data($resource))) {
            $data = [];
        }

        if (isset($data['uri']) && stream_is_local($data['uri'])) {
            return $this->readFromFile($data['uri']);
        }

        return $this->readFromBlob(stream_get_contents($resource));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        if (is_string($type)) {
            if (is_file($type) && stream_is_local($type)) {
                return true;
            }
        }

        if (is_resource($type) && 'file' === get_resource_type($type)) {
            return true;
        }

        return false;
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

        return $file + $data;
    }

    protected function getMappedKeys()
    {
        return [];
    }
}
