<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Geometry;

/**
 * @class Box
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Size implements SizeInterface
{
    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /**
     * Constructor.
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = (int)$width;
        $this->height = (int)$height;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatio()
    {
        return $this->ratio($this->width, $this->height);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function scale($percent)
    {
        return new static(
            $w = (int)(round($this->width * (float)$percent) / 100),
            //(int)(round($this->height * (float)$percent) / 100)
            (int)floor($w / $this->getRatio())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pixel($limit)
    {
        $ratio  = $this->getRatio();

        return new static(
            $w = (int)round(sqrt((float)$limit * $ratio)),
            (int)floor((float)$w / $ratio)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function contains(SizeInterface $size, PointInterface $point = null)
    {
        $point = $point ?: new Point(0, 0);

        $sw = $size->getWidth() + $point->getX();
        $sh = $size->getHeight() + $point->getY();

        if (0 > min($point->getX(), $point->getY())) {
            return false;
        }

        return $this->width >= $sw && $this->height >= $sh;
    }

    /**
     * {@inheritdoc}
     */
    public function has(PointInterface $point)
    {
        return $point->isIn($this);
    }

    /**
     * {@inheritdoc}
     */
    public function increaseByWidth($width)
    {
        $width = $this->getWidth() + (int)$width;

        return new static($width, $this->heightFromRatio($width, $this->getRatio()));
    }

    /**
     * {@inheritdoc}
     */
    public function increaseByHeight($height)
    {
        $height = $this->getHeight() + (int)$height;
        return new static($this->widthFromRatio($height, $this->getRatio()), $height);
    }

    /**
     * {@inheritdoc}
     */
    public function getSizeFromRatio($width = 0, $height = 0)
    {
        if (0 === max($width, $height)) {
            throw new \LogicException('Invalid Geometry');
        }

        if (0 === $width) {
            return new static($this->widthFromRatio($height, $this->getRatio()), $height);
        }

        if (0 === $height) {
            return new static($width, $this->heightFromRatio($width, $this->getRatio()));
        }

        throw new \LogicException('Either height, or width must be zero');
    }

    /**
     * {@inheritdoc}
     */
    public function fit(SizeInterface $target)
    {
        $width  = $this->getWidth();
        $height = $this->getHeight();

        $tw = $target->getWidth();
        $th = $target->getHeight();

        $nh = $this->heightFromRatio($tw, $this->getRatio());

        if ($nh > $th) {
            return new static($this->widthFromRatio($th, $this->getRatio()), $th);
        }

        return new static($tw, $nh);
    }

    /**
     * {@inheritdoc}
     */
    public function fill(SizeInterface $target)
    {
        $width  = $this->getWidth();
        $height  = $this->getHeight();

        $w = $target->getWidth();
        $h = $target->getHeight();

        $ratio = $this->getRatio();

        $minW = min($w, $width);
        $minH = min($h, $height);
        $minB = min($w, $h);

        if (!($minB === 0 || ($minW > $width && $minH > $height))) {
            $ratioC = $target->getRatio();

            list($w, $h) = $ratio > $ratioC ? [(int)ceil($h * $ratio), $h] : [$w, (int)ceil($w / $ratio)];
        }

        return new static($w, $h);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($deg)
    {
        while ($deg < 0) {
            $deg += 360;
        }

        $deg = (float)$deg;

        // don't need to calculate surrounding box.
        if (0 === $deg || 0 === $deg % 180.0) {
            return clone $this;
        } elseif (0 === $deg % 90.0) {
            return new static($this->getHeight(), $this->getWidth());
        }

        $rad = deg2rad($deg);

        $pw = 1.0;
        $ph = $pw / $this->getRatio();

        list ($pw, $ph) = $this->doRotate($rad, $pw, $ph);
        list ($w, $h) = $this->doRotate($rad, $this->getWidth(), $this->getHeight());

        return new static((int)round($w + $pw), (int)round($h + $ph));
    }

    /**
     * doRotate
     *
     * @param float $rad
     * @param int $w
     * @param int $h
     *
     * @return array
     */
    private function doRotate($rad, $w, $h)
    {
        return [
            ((float)$w * abs(cos($rad)) + (float)$h * abs(sin($rad))),
            ((float)$w * abs(sin($rad)) + (float)$h * abs(cos($rad)))
        ];
    }

    /**
     * ratio
     *
     * @param int $w
     * @param int $h
     *
     * @return float
     */
    private function ratio($w, $h)
    {
        return (float)($w / $h);
    }

    /**
     * heightFromRatio
     *
     * @param int $width
     * @param float $ratio
     *
     * @return int
     */
    private function heightFromRatio($width, $ratio = 1.0)
    {
        return (int)round((float)$width / $ratio);
    }

    /**
     * widthFromRatio
     *
     * @param int $height
     * @param float $ratio
     *
     * @return int
     */
    private function widthFromRatio($height, $ratio = 1.0)
    {
        return (int)round((float)$height * $ratio);
    }
}