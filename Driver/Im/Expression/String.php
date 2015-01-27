<?php

/*
 * This File is part of the Lucid\Image\Driver\Im\Expression package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Expression;

/**
 * @class String
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class String implements ExpressionInterface
{
    const M_RAW = 1;
    const M_ESC = 2;

    private $str;
    private $mode;

    /**
     * Constructor.
     *
     * @param string $str
     * @param int $mode
     */
    public function __construct($str, $mode = self::M_ESC)
    {
        $this->str = $str;
        $this->mode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return self::M_RAW === $this->mode ? $this->str : $this->format();
    }

    /**
     * format
     *
     * @return string
     */
    private function format()
    {
        $parts = explode(' ', $this->str);

        $str = implode(' ', array_map('escapeshellarg', $parts));

        return $str;
    }
}
