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

use Thapp\Image\Geometry\PointInterface;

/**
 * @class ColorAt
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ColorAt extends AbstractCommand
{
    /** @var string */
    const FMTSTR = <<<'PHP'
-crop 1x1+%s+%s -format "rgba(%%[fx:floor(255*u.r)],%%[fx:floor(255*u.g)],%%[fx:floor(255*u.b)],%%[fx:abs(u.a)])" info:
PHP;

    /** @var PointInterface */
    private $point;

    /**
     * Constructor.
     *
     * @param PointInterface $point
     */
    public function __construct(PointInterface $point)
    {
        $this->point = $point;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf(self::FMTSTR, $this->point->getX(), $this->point->getY());
    }
}
