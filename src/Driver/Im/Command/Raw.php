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
 * @class Raw
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Raw extends AbstractCommand
{
    /* @var string */
    private $string;

    /**
     * Constructor.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return $this->string;
    }
}
