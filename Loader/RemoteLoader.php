<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Loader;

/**
 * @class RemoteLoader extends AbstractLoader
 * @see AbstractLoader
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RemoteLoader extends AbstractLoader
{
    /**
     * curl_error
     *
     * @var string
     */
    private $curl_error;

    /**
     * tmp
     *
     * @var string
     */
    protected $tmp;

    /**
     * file
     *
     * @var string
     */
    protected $source;

    /**
     * Create a new RemoteLoader instance.
     */
    public function __construct()
    {
        $this->tmp = sys_get_temp_dir();
    }

    /**
     * load
     *
     * @param mixed $url
     *
     * @throws \RuntimeException if fetching remote file fails
     * @return string|boolean false if loading fails, else the downloaded file
     * as string
     */
    public function load($url)
    {
        if ($file = $this->loadRemoteFile($url)) {
            return $this->validate($file);
        }

        throw new \RuntimeException(
            sprintf('Error loading remote file "%s": %s', $url, $this->curl_error ?: 'undefined error')
        );
    }

    /**
     * supports
     *
     * @param string $url
     *
     * @return boolean
     */
    public function supports($url)
    {
        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https', 'spdy']);
    }

    /**
     * loadRemoteFile
     *
     * @param string $url
     * @return string|boolean false if error
     */
    private function loadRemoteFile($url)
    {
        $this->source = tempnam($this->tmp, basename($url));

        if (!function_exists('curl_init')) {

            if (!$contents = file_get_contents($url)) {
                return false;
            }

            file_put_contents($contents, $this->source);

            return $this->source;
        }

        $status = $this->fetchFile($handle = fopen($this->source, 'w'), $url, $this->curl_error);
        fclose($handle);

        return $status ? $this->source : $status;
    }

    /**
     * fetchFile
     *
     * @param resource $handle
     * @return int the curl status
     */
    protected function fetchFile($handle, $url, &$message = null)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FILE, $handle);

        $status = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (!in_array($info['http_code'], [200, 302, 304])) {
            $status = false;
        }

        if (0 !== strlen($msg = curl_error($curl))) {
            $message = $msg;
            $status = false;
        }

        curl_close($curl);

        return $status;
    }
}
