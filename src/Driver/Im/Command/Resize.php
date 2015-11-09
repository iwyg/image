<?php

/*
 * This File is part of the Thapp\Image package
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
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Resize extends AbstractCommand
{
    /** @var SizeInterface */
    private $size;

    /** @var Filter */
    private $filter;

    /**
     * Constructor.
     *
     * @param SizeInterface $size
     * @param Filter $filter
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
        $filter = null !== $this->filter ? ' '.$this->filter->asString() : '';

        return sprintf('-resize %sx%s%s', $this->size->getWidth(), $this->size->getHeight(), $filter);
    }
}
