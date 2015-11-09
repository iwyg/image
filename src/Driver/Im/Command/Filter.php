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

/**
 * @class Filter
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Filter extends AbstractCommand
{
    /** @var string */
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
