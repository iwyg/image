<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

use Thapp\Image\Geometry\SizeInterface;

/**
 * @class Resize
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Resize extends AbstracCommand
{
    /**
     * size
     *
     * @var SizeInterface
     */
    private $size;

    /**
     * filter
     *
     * @var Filter
     */
    private $filter;

    /**
     * Constructor.
     *
     * @param SizeInterface $size
     */
    public function __construct(SizeInterface $size, Filter $filter = null)
    {
        $this->size = $size;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        $filter = null !== $filter ? ' '.$filter->asString() : '';

        return sprintf('-resize %sx%s%s', $this->size->getWidth(), $this->size->getHeight(), $filter);
    }
}
