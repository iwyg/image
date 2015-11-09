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

use RuntimeException;
use Thapp\Image\Driver\Im\Command\File;
use Thapp\Image\Driver\Im\Shell\Command;
use Thapp\Image\Driver\Im\Command\CommandInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * @class Convert
 *
 * @package Thapp\Image
 * @version $Id$
 * @author  Thomas Appel <mail@thomas-appel.com>
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
     * @param Logger $logger
     */
    public function __construct(Command $command = null, $bin = 'convert', Logger $logger = null)
    {
        $this->command  = $command ?: new Command;
        $this->logger   = $logger;
        $this->bin      = $bin ?: 'convert';
        $this->ops      = [];
        $this->compiled = false;
    }

    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->clean();
    }

    /**
     * Get the executable of the command.
     *
     * @return string
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * Get the shell Command object.
     *
     * @return Command
     */
    public function getShellCommand()
    {
        return $this->command;
    }

    /**
     * clean up current commands.
     *
     * @return void
     */
    public function clean()
    {
        $this->ops      = [];
        $this->compiled = false;
        $this->source   = null;
        $this->dest     = null;
    }

    /**
     * Sets the source file path.
     *
     * @param File $file
     *
     * @return void
     */
    public function setSource(File $file)
    {
        $this->compiled = false;
        $this->source   = $file;
    }

    /**
     * Sets the target file path.
     *
     * @param File $file
     *
     * @return void
     */
    public function setTarget(File $file)
    {
        $this->compiled = false;
        $this->dest     = $file;
    }

    /**
     * Add a command.
     *
     * @param CommandInterface $ops
     *
     * @return void
     */
    public function addCommand(CommandInterface $ops)
    {
        $this->compiled = false;
        $this->ops[]    = $ops;
    }

    /**
     * Run the command.
     *
     * @param File $source
     * @param string $target the target path
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
            $ret = $this->command->run($cmd, '\RuntimeException', null, explode(' ', '\[ \] " % { }, [ ] \( \) \*'));
        } catch (RuntimeException $e) {
            throw new RuntimeException(
                sprintf('Executing command "%s" failed.', $cmd),
                $e->getCode(),
                $e
            );
        }

        if (null !== $this->logger) {
            $this->logRun($ret, $cmd);
        }

        return $ret;
    }

    /**
     * Log command exectution
     *
     * @param string|bool $ret the command result.
     * @param string $cmd
     *
     * @return void
     */
    private function logRun($ret, $cmd)
    {
        if (false === $ret) {
            $this->logger->warn(sprintf('Imagemagick failed with command: %s', $cmd));
        } else {
            $this->logger->debug(sprintf('Imagemagick: %s', $cmd));
        }
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
            throw new RuntimeException('Target file is missing.');
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
