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
 * @class Source
 *
 * @package Lucid\Image\Driver\Im\Expression
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source implements ExpressionInterface
{
    private $source;
    private $type;

    public function __construct($source, $type = 'JPEG')
    {
        $this->source = $source;
        $this->type = $type;
    }

    public function __toString()
    {
        return sprintf('%s:%s', $this->getType(), $this->getSource());
    }

    protected function getType()
    {
        return strtoupper($this->type);
    }

    protected function getSource()
    {
        return $this->source;
    }
}
