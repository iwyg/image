<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source
{
    public function read($resource)
    {
        $meta = stream_get_meta_data($resource);

        if (isset($meta['uri']) && stream_is_local($meta['uri'])) {
            fclose($resource);

            return new Image($meta['uri']);
        }

        $tmp = tmpfile();
        fwrite(stream_get_contents($resource));

        return $this->read($tmp);
    }
}
