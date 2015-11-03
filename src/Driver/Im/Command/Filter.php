<?php

/*
 * This File is part of the Thapp\Image\Driver\Im\Command package
 *
 * (c)  <>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

/**
 * @class Filter
 *
 * @package Thapp\Image\Driver\Im\Command
 * @version $Id$
 * @author  <>
 */
class Filter extends AbstractCommand
{
    /**
     * filter
     *
     * @var string
     */
    private $filter;

    /**
     * Constructor.
     *
     * @param string $filter
     */
    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-filter %s', $this->filter);
    }
}
