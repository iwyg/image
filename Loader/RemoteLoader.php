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

use Thapp\Image\Exception\SourceLoaderException;

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
    private $error;

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
     * source
     *
     * @var array
     */
    protected $trustedHosts;

    /**
     * Create a new RemoteLoader instance.
     */
    public function __construct(array $trustedHosts = [])
    {
        $this->trustedHosts = $trustedHosts;
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

        throw new SourceLoaderException(
            sprintf('Error loading remote file "%s": %s', $url, $this->error ?: 'undefined error')
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
        if (!$this->isValidDomain($url)) {
            $this->error = sprintf('forbidden host "%s"', parse_url($url, PHP_URL_HOST));

            return false;
        }

        $this->source = tempnam($this->tmp, basename($url));

        if (!function_exists('curl_init')) {

            if (!$contents = file_get_contents($url)) {
                return false;
            }

            file_put_contents($contents, $this->source);

            return $this->source;
        }

        $status = $this->fetchFile($handle = fopen($this->source, 'w'), $url);
        fclose($handle);

        return $status ? $this->source : $status;
    }

    /**
     * fetchFile
     *
     * @param resource $handle
     * @param string $url
     * @return int the curl status
     */
    private function fetchFile($handle, $url)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FILE, $handle);

        $status = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (!in_array($info['http_code'], [200, 302, 304])) {
            $this->error = 'resource not found';
            $status = false;
        }

        if (0 !== strlen($msg = curl_error($curl))) {
            $this->error = $msg;
            $status = false;
        }

        curl_close($curl);

        return $status;
    }

    /**
     * isValidDomain
     *
     * @access protected
     * @return string|boolean
     */
    private function isValidDomain($url)
    {
        $trusted = $this->trustedHosts;

        if (!empty($trusted)) {

            $host = parse_url($url, PHP_URL_HOST);
            $host = substr($url, 0, strpos($url, $host)).$host;

            if (!$this->matchHost($host, $trusted)) {
                return false;
            }
        }
        return $url;
    }

    /**
     * matchHosts
     *
     * @param mixed $host
     * @param array $hosts
     *
     * @access protected
     * @return boolean
     */
    protected function matchHost($host, array $hosts)
    {
        foreach ($hosts as $trusted) {
            if (0 === strcmp($host, $trusted) || preg_match('#^'. $trusted .'#s', $host)) {
                return true;
            }
        }
        return false;
    }
}
