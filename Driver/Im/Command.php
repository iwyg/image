<?php

/*
 * This File is part of the Lucid\Image\Driver\Im package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Symfony\Component\Process\Process;
use Thapp\Image\Driver\Im\Expression\Bin;
use Thapp\Image\Driver\Im\Expression\ExpressionInterface;

/**
 * @class Expression
 *
 * @package Lucid\Image\Driver\Im
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Command implements \Countable
{
    private $expr = [];
    private $bin;
    private $compiled;

    /**
     * Constructor.
     *
     * @param string $bin
     */
    public function __construct(Bin $bin = null, $proc = null)
    {
        $this->bin = $bin ?: new Bin('convert');
        $this->compiled = null;
    }

    public function count()
    {
        return count($this->expr);
    }

    public function add(ExpressionInterface $expr)
    {
        $this->expr[] = $expr;
    }

    public function insert(ExpressionInterface $expr, $index)
    {
        if ($index + 1 > count($this->expr) || $index + 1 < count($this->expr)) {
            //throw new \OutOfBoundsException(sprintf('Invalid index %s.', $index));
        }

        array_splice($this->expr, $index, 0, [$expr]);
    }

    public function compile()
    {
        $cmd = [];

        $this->insert($this->bin, 0);

        foreach ($this->expr as $expr) {
            $cmd[] = (string)$expr;
        }

        $this->compiled = implode(' ', $cmd);
        $this->expr = [];
    }

    public function getCommand()
    {
        return $this->compiled ?: '';
    }

    public function clean()
    {
        $this->expr = [];
        $this->compiled = null;
    }

    public function run()
    {
        if (null === $this->compiled) {
            throw new LogicException();
        }

        $proc = new Process($this->compiled);

        $proc->run();

        return $proc;
    }
}
