<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Im;

use Thapp\Image\Driver\AbstractImage;
use Thapp\Image\Info\MetaData;
use Thapp\Image\Info\MetaDataInterface;
use Thapp\Image\Color\Palette\PaletteInterface;
use Thapp\Image\Color\Profile\ProfileInterface;
use Thapp\Image\Color\ColorInterface;
use Thapp\Image\Driver\Im\Command\Profile;
use Thapp\Image\Driver\Im\Command\Canvas;
use Thapp\Image\Driver\Im\Command\Size as SizeCmd;
use Thapp\Image\Driver\Im\Command\Colorspace;
use Thapp\Image\Driver\Im\Command\File;
use Thapp\Image\Driver\Im\Command\ColorAt;
use Thapp\Image\Driver\Im\Command\CommandInterface;
use Thapp\Image\Geometry\PointInterface;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\SizeInterface;
use Thapp\Image\Exception\ImageException;

/**
 * @class Image
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Image extends AbstractImage implements ImageInterface
{
    /** @var SizeInterface */
    private $size;

    /** @var string */
    private $file;

    /** @var string */
    private $fileFormat;

    /** @var bool */
    private $hasProfile;

    /** @var array */
    private $tmp;

    /** @var int */
    private $numberImages;

    /** @var Convert */
    private $convert;

    /** @var array */
    private static $cspaceMap = [
        PaletteInterface::PALETTE_RGB       => 'RGB',
        PaletteInterface::PALETTE_CMYK      => 'CMYK',
        PaletteInterface::PALETTE_GRAYSCALE => 'Gray'
    ];

    /**
     * Constructor.
     *
     * @param array $imageInfo
     * @param PaletteInterface $palette
     * @param MetaDataInterface $metaData
     * @param Convert $convert
     */
    public function __construct(array $imageInfo, PaletteInterface $palette, MetaDataInterface $metaData = null, Convert $convert = null)
    {
        $this->setImageInfo($imageInfo);

        $this->palette = $palette;
        $this->meta    = $metaData ?: new MetaData;
        $this->convert = $convert ?: new Convert;
        $this->frames  = new Frames($this);
        $this->tmp     = [];
    }

    /**
     * Empty temp files
     */
    public function __destruct()
    {
        $this->sweepTmp();
    }

    /**
     * Clones this instance.
     */
    public function __clone()
    {
        $this->size    = clone $this->size;
        $this->palette = clone $this->palette;
        $this->meta    = clone $this->meta;
        $this->convert = clone $this->convert;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        $this->file = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === $this->size) {
            $info = getimagesize($this->file);
            $this->size = new Size($info[0], $info[1]);
        }

        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->getSize()->getWidth();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->getSize()->getHeight();
    }

    /**
     * {@inheritdoc}
     */
    public function applyProfile(ProfileInterface $profile)
    {
        $this->addCommand(new Profile($profile));
    }

    /**
     * {@inheritdoc}
     */
    public function applyPalette(PaletteInterface $palette)
    {
        if (!$this->hasProfile) {
            $this->applyProfile($this->palette->getProfile());
        }

        $this->addCommand(new Colorspace($palette));
    }

    /**
     * {@inheritdoc}
     * @TODO: this will only return the color of the source file and ignore all
     * prior modifications
     */
    public function getColorAt(PointInterface $pixel)
    {
        if (!$this->getSize()->has($pixel)) {
            throw new \OutOfBoundsException('Pixel position is outside of image.');
        }

        $conv = new Convert($this->convert->getShellCommand(), $this->convert->getBin());

        $conv->addCommand(new ColorAt($pixel));
        $conv->setSource(new File($this->file));
        $conv->setTarget(new File(''));

        try {
            $color = $conv->run();
        } catch (\RuntimeException $e) {
            throw new ImageException('Cannot extract color.', $e->getCode(), $e);
        }

        return $this->palette->getColor($color);
    }

    /**
     * Adds a new option to the convert command list.
     *
     * @param CommandInterface $command
     * @param SizeInterface $size
     *
     * @return void
     */
    public function addCommand(CommandInterface $command, SizeInterface $size = null)
    {
        $this->convert->addCommand($command);

        if (null !== $size) {
            $this->size = $size;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrames()
    {
        return 1 < $this->numberImages;
    }

    /**
     * {@inheritdoc}
     */
    public function frames()
    {
        return $this->frames;
    }

    /**
     * {@inheritdoc}
     */
    public function newImage($format = null, ColorInterface $background = null)
    {
        $format = null !== $format ? $this->mapFormat($format) : $this->getFormat();
        $size   = $this->size;
        $target = $this->getTempFilename();
        $conv   = new Convert($this->convert->getShellCommand(), $this->convert->getBin());

        $conv->addCommand(new SizeCmd($size));
        $conv->addCommand(new Canvas($background));
        $conv->addCommand(new Colorspace($this->palette));
        $conv->setTarget(new File($target, $format));

        try {
            $conv->run();
        } catch (\RuntimeException $e) {
            throw new ImageException('Unable to create new image.', $e->getCode(), $e);
        }

        $conv->clean();

        return new self($this->newInfo($target, $format, $size, $this->palette), clone $this->palette, new MetaData([]), $conv);
    }


    /**
     * Get the source file path.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function save($target, $format = null, array $options = [])
    {
        $options = $this->prepareOpts($format, $options);
        $this->convert->setSource(new File($this->file, $this->fileFormat));
        $this->convert->setTarget(new File($target, $options['format']));

        try {
            $this->convert->run();
        } catch (\RuntimeException $e) {
            throw new ImageException('Couldn\'t save image.', $e->getCode(), $e);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($stream, $format = null, array $options = [])
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new ImageException('Couldn\'t write image data to stream. Stream is invalid.');
        }

        // fwrite doesn't return false if stream is read only. Instead it just
        // writes 0 bytes.
        if (0 !== fwrite($stream, $this->getBlob($format, $options))) {
            return true;
        }

        throw new ImageException('Couldn\'t write image data to stream. Stream is not writable.');
    }


    /**
     * {@inheritdoc}
     */
    public function getBlob($format = null, array $options = [])
    {
        $options = $this->prepareOpts($format, $options);
        $tmp = $this->getTempFilename();

        $this->convert->setSource(new File($this->file, $this->fileFormat));
        $this->convert->setTarget(new File($tmp, $options['format']));

        try {
            $this->convert->run();
        } catch (\RuntimeException $e) {
            throw new ImageException('Operation failed.', $e->getCode(), $e);
        }

        $content = file_get_contents($tmp);

        @unlink($tmp);

        return $content;
    }

    /**
     * prepareOpts
     *
     * @param string $format
     * @param array $options
     *
     * @return array
     */
    private function prepareOpts($format = null, array $options = [])
    {
        $format = $this->getOption($options, 'format', $this->getFormat());
        $options['format'] = $format;

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function newEdit()
    {
        return new Edit($this);
    }

    /**
     * @return array
     */
    protected function &getInterlaceMap()
    {
        return [];
    }

    /**
     * Initialize image info attributes.
     *
     * @param array $info
     * @throws \InvalidArgumentException if $info miesses an attribute
     *
     * @return void
     */
    private function setImageInfo(array $info)
    {
        foreach (['file', 'width', 'height', 'icc', 'icm', 'format', 'frames'] as $attr) {
            if (!array_key_exists($attr, $info)) {
                throw new \InvalidArgumentException(sprintf('Invalid image info: expected to see "%s"', $attr));
            }
        }

        $this->file         = $info['file'];
        $this->size         = new Size($info['width'], $info['height']);
        $this->format       = $info['format'];
        $this->fileFormat   = $info['format'];
        $this->hasProfile   = null !== $info['icc'] || null !== $info['icm'];
        $this->numberImages = $info['frames'];
    }

    /**
     * newInfo
     *
     * @param string $file
     * @param SizeInterface $size
     * @param PaletteInterface $palette
     *
     * @return array
     */
    private function newInfo($file, $format, SizeInterface $size, PaletteInterface $palette)
    {
        $info = [
            'file'       => $file,
            'format'     => strtoupper($format),
            'width'      => $size->getWidth(),
            'height'     => $size->getHeight(),
            'colorspace' => $this->mapColorspace($palette),
            'frames'     => 1,
            'icc'        => null,
            'icm'        => null
        ];

        $info[$palette->getProfile()->getName()] = true;

        return $info;
    }

    /**
     * mapColorspace
     *
     * @param PaletteInterface $palette
     *
     * @return string
     */
    private function mapColorspace(PaletteInterface $palette)
    {
        if (!isset(static::$cspaceMap[$palette->getConstant()])) {
            throw new \InvalidArgumentException;
        }

        return static::$cspaceMap[$palette->getConstant()];
    }

    /**
     * getTempFilename
     *
     * @return string
     */
    private function getTempFilename()
    {
        return $this->tmp[] = sys_get_temp_dir().DIRECTORY_SEPARATOR.hash('md5', 'imagemagick_tmp'.microtime(true));
    }

    /**
     * Delete temp files
     *
     * @return void
     */
    private function sweepTmp()
    {
        foreach ($this->tmp as $file) {
            if (!is_file($file)) {
                continue;
            }

            @unlink($file);
        }
    }
}
