<?php

/*
 * This File is part of the Thapp\Image\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Geometry;

/**
 * @class Gravity
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Gravity implements GravityInterface
{
    /**
     * mode
     *
     * @var int
     */
    private $mode;

    /**
     * Constructor.
     *
     * @param int $mode
     */
    public function __construct($mode)
    {
        $this->mode = max(self::GRAVITY_NORTHWEST, min(self::GRAVITY_SOUTHEAST, (int)$mode));
    }

    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPoint(SizeInterface $source, SizeInterface $target)
    {
        list($x, $y) = $this->getCropFromGravity($source, $target);

        return new Point($x, $y);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCropFromGravity(SizeInterface $source, SizeInterface $box)
    {
        $x = $y = 0;

        $width  = $source->getWidth();
        $height = $source->getHeight();

        $w = $box->getWidth();
        $h = $box->getHeight();

        switch ($this->getMode()) {
            case self::GRAVITY_NORTHWEST:
                break;
            case self::GRAVITY_NORTH:
                $x = ($width / 2) - ($w / 2);
                break;
            case self::GRAVITY_NORTHEAST:
                $x = ($width) - $w;
                break;
            case self::GRAVITY_WEST:
                $y = ($height / 2) - ($h / 2);
                break;
            case self::GRAVITY_CENTER:
                $x = ($width / 2) - ($w / 2);
                $y = $height / 2  - ($h / 2);
                break;
            case self::GRAVITY_EAST:
                $x = $width - $w;
                $y = ($height / 2)  - ($h / 2);
                break;
            case self::GRAVITY_SOUTHWEST:
                $x = 0;
                $y = $height - $h;
                break;
            case self::GRAVITY_SOUTH:
                $x = ($width / 2) - ($w / 2);
                $y = $height - $h;
                break;
            case self::GRAVITY_SOUTHEAST:
                $x = $width - $w;
                $y = $height - $h;
                break;
        }

        return [(int)ceil($x), (int)ceil($y)];
    }
}
