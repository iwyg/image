<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Color\Profile\ProfileInterface;

/**
 * @class Profile
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Profile extends AbstractCommand
{
    private $path;
    private $profile;

    /**
     * Constructor.
     *
     * @param ProfileInterface $profile
     */
    public function __construct(ProfileInterface $profile)
    {
        $this->profile = $profile;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-profile %s', $this->profilePath());
    }

    /**
     * profilePath
     *
     * @return string
     */
    private function profilePath()
    {
        if (null === $this->path) {

            if (!stream_is_local($path = $this->profile->getFile())) {
                $tmp = tmpfile();
                $meta = stream_get_meta_data($tmp);

                stream_copy_to_stream($stream = fopen($path, 'r'), $tmp);
                fclose($stream);

                $path = $meta['url'];
            }

            $this->path = $path;
        }

        return realpath($this->path);
    }
}
