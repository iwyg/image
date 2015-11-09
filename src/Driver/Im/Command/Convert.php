<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im\Command;

/**
 * @class Convert
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Convert extends AbstractCommand
{
    /**
     * Constructor
     *
     * @param File $input
     * @param File $output
     * @param string $expression
     * @param string $binpath
     */
    public function __construct(File $input, File $output, $expression, $binpath)
    {
    }
}
