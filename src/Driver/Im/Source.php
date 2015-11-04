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

use Thapp\Image\Driver\AbstractSource;
use Thapp\Image\Info\MetaDataReaderInterface;
use Thapp\Image\Exception\ImageException;
use Thapp\Image\Color\Palette\Rgb;
use Thapp\Image\Color\Palette\Cmyk;
use Thapp\Image\Color\Palette\Grayscale;

/**
 * @class Source
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source extends AbstractSource
{
    private $identify;
    private $convert;

    /**
     * Constructor.
     *
     * @param MetaDataReaderInterface $reader
     * @param Identify $identify
     */
    public function __construct(MetaDataReaderInterface $reader = null, Identify $identify = null, Convert $conv = null)
    {
        parent::__construct($reader);
        $this->identify = $identify ?: new Identify;
        $this->convert = $conv ?: new Convert;
    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        if (!is_file($file) || !stream_is_local($file)) {
            throw new ImageException(sprintf('Cannot load image "%s". No such file or directory', $file));
        }

        try {
            $info = $this->identify->identify($file);
            return new Image($info, $this->getImagePalette($info), $this->getReader()->readFromFile($file), clone $this->convert);

        } catch (\Exception $e) {
            throw ImageException::load($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($stream)
    {
        $this->validateStream($stream);

        try {
            $url = $this->streamUrl($stream);

            return $this->load($url);
        } catch (ImageException $e) {
            throw ImageException::read($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $stream = tmpfile();

        fwrite($stream, $image);

        try {
            return $this->read($stream);
        } catch (ImageException $e) {
            throw ImageException::create($e);
        }
    }

    /**
     * streamUrl
     *
     * @param resource $stream
     *
     * @return string
     */
    private function streamUrl($stream)
    {
        $meta = stream_get_meta_data($stream);

        if (isset($meta['uri']) && stream_is_local($meta['uri'])) {
            return $meta['uri'];
        }

        $tmp = tmpfile();
        stream_copy_to_stream($stream, $tmp);

        return $meta['uri'];
    }

    /**
     * getImagePalette
     *
     * @param array $info
     *
     * @return Thapp\Image\Color\PaletteInterface
     */
    private function getImagePalette(array $info)
    {
        switch (strtolower($info['colorspace'])) {
            case 'srgb':
            case 'rgb':
                return new Rgb;
            case 'cmyk':
                return new Cmyk;
            case 'gray':
                return new Grayscale;
        }

        throw new ImageException(sprintf('Colorspace %s is currently not supported', $info['colorspace']));
    }
}
