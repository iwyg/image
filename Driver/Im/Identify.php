<?php

/*
 * This File is part of the Thapp\Image\Driver\Im package
 *
 * (c)  <>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Driver\Im\Shell\Command;

/**
 * @class Identify
 *
 * @package Thapp\Image\Driver\Im
 * @version $Id$
 * @author  <>
 */
class Identify
{
    private $bin;
    private $command;

    /**
     * Constructor.
     *
     * @param Command $command
     * @param string $bin
     */
    public function __construct(Command $command = null, $bin = 'identify')
    {
        $this->command = $command ?: new Command;
        $this->bin = $bin ?: 'identify';
    }

    public function identify($file)
    {
        $cmd = sprintf(
            '%s -ping -format colorspace=%%r\ntype=%%[type]\nwidth=%%w\nheight=%%h\nformat=%%m\nextension=%%e\nicc=%%[profile:icc]\nicm=%%[profile:icm]\nframes=%%n %s',
            $this->bin,
            $file
        );

        try {
            $ret  = $this->command->run($cmd);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(sprintf('Cant identify image, %s', $file), $e->getCode(), $e);
        }

        $data = $this->parse($ret);
        $data['file'] = realpath($file);

        return $data;
    }

    private function parse($result)
    {
        $out = [];
        foreach (explode("\n", $result) as $value) {
            list ($key, $val) = explode('=', $value);

            if ('colorspace' === $key) {
                list (, $val) = explode(" ", $val);
            } elseif (is_numeric($val)) {
                $val = (int)$val;
            } elseif ("" === $val) {
                $val = null;
            }

            $out[$key] = $val;

        }

        return $out;
    }

    private function parseList($list, &$result = [])
    {
        foreach ($list as $value) {
        }
    }
}
