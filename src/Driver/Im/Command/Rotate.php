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
 * @class Rotate
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Rotate extends AbstractCommand
{
    /** @var float */
    private $deg;

    /**
     * Constructor.
     *
     * @param float $deg
     */
    public function __construct($deg)
    {
        $this->deg = (float)$deg;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-rotate %s', (string)$this->deg);
    }
}
