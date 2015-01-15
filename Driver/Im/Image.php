<?php

/*
 * This File is part of the Thapp\Image\Driver\Im package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\BoxInterface;
use Thapp\Image\Metrics\PointInterface;
use Thapp\Image\Metrics\GravityInterface;
use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Driver\Im\Expression\Extent;
use Thapp\Image\Driver\Im\Expression\Source as ExSource;
use Thapp\Image\Driver\Im\Expression\Target;
use Thapp\Image\Driver\Im\Expression\Rotate;

/**
 * @class Driver
 *
 * @package Thapp\Image\Driver\Im
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage
{
    /**
     * size
     *
     * @var BoxInterface
     */
    private $size;
    private $file;
    private $info;
    private $convert;
    private $tmp;

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->tmp = [];
        $this->file = $file;
        $this->frames = new Frames($this);
        $this->command = new Command;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->tmp as $file) {
            @unlink($file);
        }
    }

    public function frames()
    {
        return $this->frames;
    }

    public function resize(BoxInterface $size)
    {
        $this->updateGeometry(clone $size);
    }

    public function getWidth()
    {
        return $this->size->getWidth();
    }

    public function getHeight()
    {
        return $this->size->getWidth();
    }

    public function getFormat()
    {
    }

    public function setFormat($format)
    {
    }

    public function getSize()
    {
        if (null === $this->size) {
            $size = getimagesize($this->file);
            $this->size = new Box($size[0], $size[1]);
        }

        return $this->size;
    }

    public function crop(BoxInterface $size, PointInterface $start = null)
    {
        $this->updateGeometry(clone $size);
    }

    public function extent(BoxInterface $size, PointInterface $start = null)
    {
        $this->command->add(new Extent($size, $start ?: new Point(0, 0)));
        $this->updateGeometry(clone $size);
    }

    public function rotate($deg)
    {
        $this->command->add(new Rotate($deg));
        $this->updateGeometry($this->getSize()->rotate($deg));
    }

    public function gravity(GravityInterface $gravity)
    {
        $this->command->add(new GravityEx($gravity));
    }

    /**
     * get
     *
     * @param mixed $format
     * @param array $options
     *
     * @return void
     */
    public function get($format = null, array $options = [])
    {
        $this->tmp[] = $target = tempnam(sys_get_temp_dir(), 'im_');

        $this->command->insert(new ExSource($this->file, $this->getImSourceFormat()), 0);
        $this->command->add(new Target($target, $this->getImTargetFormat()));
        $this->command->compile();
        //var_dump($this->command->getCommand());
        $this->command->run();

        return file_get_contents($target);
    }

    /**
     * getImSourceFormat
     *
     *
     * @return void
     */
    private function getImSourceFormat()
    {
        return 'JPEG';
    }

    /**
     * getImTargetFormat
     *
     *
     * @return void
     */
    private function getImTargetFormat()
    {
        return 'JPEG';
    }

    /**
     * updateGeometry
     *
     * @param BoxInterface $size
     *
     * @return void
     */
    protected function updateGeometry(BoxInterface $size)
    {
        $this->size = $size;
    }
}
