<?php

/*
 * This File is part of the Thapp\Image\Metrics package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Metrics;

/**
 * @class Box
 *
 * @package Thapp\Image\Metrics
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Box implements BoxInterface
{
    private $width;
    private $height;

    /**
     * Constructor.
     *
     * @param mixed $width
     * @param mixed $height
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
    public function contains(BoxInterface $box, PointInterface $point = null)
    {
        $point = $point ?: new Point(0, 0);

        return $this->width >= $box->getWidth() + $point->getX() && $this->height >= $box->getHeight() + $point->getY();
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
        return new static($width, (float)$width / $this->getRatio());
    }

    /**
     * {@inheritdoc}
     */
    public function increaseByHeight($height)
    {
        return new static($height * $this->getRatio(), $height);
    }

    /**
     * {@inheritdoc}
     */
    public function fit(BoxInterface $target)
    {
        $width  = $this->getWidth();
        $height = $this->getHeight();

        $w = $target->getWidth();
        $h = $target->getHeight();

        $ratA = $this->ratio($width, $height);

        if ($width >= $w && $height >= $h) {

            $ratM = min($ratA, $this->ratio($w, $h));

            $valW = (int)round($h * $ratM);
            $valH = (int)round($w / $ratA);

            $isR = $ratM === $ratA;

            list($width, $height) = $width <= $height ? [$valW, $isR ? $h : $valH] : [$isR ? $w : $valW, $valH];
        } elseif ($width >= $w && $height < $h) {
            $width = $w;
            $height = (int)round($w / $ratA);
        } elseif ($width < $w || $height < $h) {
            $box = $this->increaseByWidth($w);

            if ($h < $box->getHeight()) {
                return $box->increaseByHeight($h);
            }

            return $box;
        }

        return new static($width, $height);
    }

    /**
     * {@inheritdoc}
     */
    public function fill(BoxInterface $target)
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

        // don't need to caclulate surrounding box.
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
}
