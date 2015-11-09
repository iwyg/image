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
 * @class Size
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Size extends AbstractCommand
{
    /** @var SizeInterface */
    private $size;

    /**
     * Constructor.
     *
     * @param SizeInterface $size
     */
    public function __construct(SizeInterface $size)
    {
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-size %sx%s', $this->size->getWidth(), $this->size->getHeight());
    }
}
