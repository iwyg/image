<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel  <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Driver\Im\Shell\Command;

/**
 * @class Identify
 *
 * @package Thapp\Image
 * @version $Id$
 * @author  Thomas Appel <mail@thomas-appel.com>
 */
class Identify
{
    /** @var string */
    const FMTSTR = <<<'PHP'
%s -ping -format colorspace=%%r
type=%%[type]
width=%%w
height=%%h
format=%%m
extension=%%e
icc=%%[profile:icc]
icm=%%[profile:icm]
frames=%%n %s
PHP;

    /** @var string */
    private $bin;

    /** @var Command */
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

    /**
     * Perform imagemagick identify
     *
     * @param string $file
     *
     * @return array
     */
    public function identify($file)
    {
        $cmd = sprintf(self::FMTSTR, $this->bin, $file);

        try {
            $ret  = $this->command->run($cmd);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(sprintf('Cant identify image, %s', $file), $e->getCode(), $e);
        }

        $data = $this->parse($ret);
        $data['file'] = realpath($file);

        return $data;
    }

    /**
     * parse
     *
     * @param string $result
     *
     * @return array
     */
    private function parse($result)
    {
        $out = [];
        foreach (explode(PHP_EOL, trim($result)) as $value) {
            list($key, $val) = explode('=', $value);

            if ('colorspace' === $key) {
                list(, $val) = explode(' ', $val);
            } elseif (is_numeric($val)) {
                $val = (int)$val;
            } elseif ('' === $val) {
                $val = null;
            }

            $out[$key] = $val;
        }

        return $out;
    }
}
