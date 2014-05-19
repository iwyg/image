<?php

/**
 * This File is part of the Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Loader;

/**
 * @class RemoteLoader
 * @package Loader
 * @version $Id$
 */
class RemoteLoader extends AbstractLoader
{
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
    protected $file;

    public function __construct()
    {
        $this->tmp = sys_get_temp_dir();
    }

    /**
     * load
     *
     * @param mixed $url
     *
     * @access public
     * @return string|boolean false if loading fails, else the downloaded file
     * as string
     */
    public function load($url)
    {
        if ($file = $this->loadRemoteFile($url)) {
            return $this->validate($file);
        }

        return false;
    }

    /**
     * supports
     *
     * @param mixed $url
     *
     * @access public
     * @return mixed
     */
    public function supports($url)
    {
        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https', 'spdy']);
    }

    /**
     * loadRemoteFile
     *
     * @param mixed $url
     * @access protected
     * @return mixed
     */
    protected function loadRemoteFile($url)
    {
        $this->file = tempnam($this->tmp, 'img_rmt_');

        if (!function_exists('curl_init')) {

            if (!$contents = file_get_contents($url)) {
                return false;
            }

            file_put_contents($contents, $this->file);

            return $this->file;

        }

        $handle = fopen($this->file, 'w');

        if (!$this->fetchFile($handle, $url)) {
            fclose($handle);
            return false;
        }

        fclose($handle);

        return $this->file;
    }

    /**
     * fetchFile
     *
     * @param Resource $handle
     * @access protected
     * @return void
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
