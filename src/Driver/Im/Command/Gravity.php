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

use Thapp\Image\Geometry\GravityInterface;

/**
 * @class Extent
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Gravity extends AbstractCommand
{
    /** @var GravityInterface */
    private $gravity;

    /**
     * Constructor.
     *
     * @param GravityInterface $gravity
     */
    public function __construct(GravityInterface $gravity)
    {
        $this->gravity = $gravity;
    }

    /**
     * {@inheritdoc}
     */
    public function asString()
    {
        return sprintf('-gravity %s', $this->translateMode($this->gravity()));
    }

    /**
     * translateMode
     *
     * @param GravityInterface $gravity
     *
     * @return string
     */
    private function translateMode(GravityInterface $gravity)
    {
        switch ($gravity->getMode()) {
            case GravityInterface::GRAVITY_NORTHWEST:
                return 'NorthWest';
            case GravityInterface::GRAVITY_NORTH:
                return 'North';
            case GravityInterface::GRAVITY_NORTHEAST:
                return 'NorthEast';
            case GravityInterface::GRAVITY_WEST:
                return 'West';
            case GravityInterface::GRAVITY_CENTER:
                return 'Center';
            case GravityInterface::GRAVITY_EAST:
                return 'East';
            case GravityInterface::GRAVITY_SOUTHWEST:
                return 'SouthWest';
            case GravityInterface::GRAVITY_SOUTH:
                return 'South';
            case GravityInterface::GRAVITY_SOUTHEAST:
                return 'SouthEast';
        }
    }
}
