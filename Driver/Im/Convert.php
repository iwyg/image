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

use Thapp\Image\Driver\Im\Command\File;
use Thapp\Image\Driver\Im\Shell\Command;
use Thapp\Image\Driver\Im\Command\CommandInterface;

/**
 * @class Convert
 *
 * @package Thapp\Image\Driver\Im
 * @version $Id$
 * @author  <>
 */
class Convert
{
    private $command;
    private $cmd;
    private $bin;
    private $ops;
    private $source;
    private $dest;
    private $compiled;

    /**
     * Constructor.
     *
     * @param Command $command
     * @param string $bin
     */
    public function __construct(Command $command = null, $bin = 'convert')
    {
        $this->command = $command ?: new Command;
        $this->bin = $bin ?: 'convert';
        $this->ops = [];
    }

    /**
     * getBin
     *
     * @return string
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * getShellCommand
     *
     * @return Command
     */
    public function getShellCommand()
    {
        return $this->command;
    }

    /**
     * clean
     *
     * @return void
     */
    public function clean()
    {
        $this->ops = [];
        $this->compiled = false;
        $this->source = null;
        $this->dest = null;
    }

    /**
     * run
     *
     * @param File $source
     * @param mixed $target
     * @param array $options
     *
     * @return boolean
     */
    public function run(File $source = null, $target = null, array $options = null)
    {
        if (null !== $source) {
            $this->setSource($source);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }

        $cmd = $this->compile();

        try {
            $ret = $this->command->run($cmd, '\RuntimeException', null, explode(' ', '\[ \] " % { }, [ ]'));
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(
                sprintf('Executing command "%s" failed.', $cmd),
                $e->getCode(),
                $e
            );
        }

        return $ret;
    }

    /**
     * setSource
     *
     * @param File $file
     *
     * @return void
     */
    public function setSource(File $file)
    {
        $this->compiled = false;
        $this->source = $file;
    }

    /**
     * setTarget
     *
     * @param File $file
     *
     * @return void
     */
    public function setTarget(File $file)
    {
        $this->compiled = false;
        $this->dest = $file;
    }

    /**
     * addCommand
     *
     * @param CommandInterface $ops
     *
     * @return void
     */
    public function addCommand(CommandInterface $ops)
    {
        $this->compiled = false;
        $this->ops[] = $ops;
    }

    /**
     * compile
     *
     * @return string
     */
    private function compile()
    {
        if ($this->compiled) {
            return $this->cmd;
        }

        if (null === $this->dest) {
            //throw new \RuntimeException('Target file is missing.');
        }

        $ops = $this->ops;

        if (null !== $this->source) {
            array_unshift($ops, $this->source);
        }

        if (null !== $this->dest) {
            $ops[] = $this->dest;
        }

        $cmd = sprintf("%s %s", $this->bin, implode(' ', $ops));
        $this->compiled = true;

        return $this->cmd = $cmd;
    }
}
