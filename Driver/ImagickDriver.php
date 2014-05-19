<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use \Imagick;
use \ImagickPixel;
use \Thapp\Image\Driver\Loader\LoaderInterface;

/**
 * Imagick Processing Driver
 *
 * @implements DriverInterface
 * @uses Scaling
 * @package Thapp\Image
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ImagickDriver extends AbstractDriver
{
    /**
     * driverType
     *
     * @var string
     */
    protected static $driverType = 'imagick';

    /**
     * resource
     *
     * @var \Imagick
     */
    protected $resource;

    /**
     * Create a new Imagick based processing driver.
     *
     * @param BinLocatorInterface $locator the source loader.
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->tmp  = sys_get_temp_dir();
        $this->loader = $loader;
    }

    /**
     * Load the source file and create the Imagick resource.
     *
     * @param  string $source the image source.
     * @access public
     * @return boolean true if the resource was created, otherwise false.
     */
    public function load($source)
    {
        if (parent::load($source)) {
            $this->resource = new Imagick($this->source);
        }

        return (bool)$this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        if ($this->resource instanceof Imagick) {
            $this->resource->destroy();
        }

        return parent::clean();
    }

    protected function cleanUpTmp()
    {
        if (is_file($this->tmpFile)) {
            @unlink($this->tmpFile);
        }
    }

    /**
     * Tear down the image and unload the image resourec.
     *
     * Calls `clean()`
     */
    public function __destruct()
    {
        parent::__destruct();

        if ($this->resource instanceof Imagick) {
            $this->resource->destroy();
        }
    }

    /**
     * getImageResource
     *
     * @access public
     * @return \Imagick the resource instance.
     */
    public function &getResource()
    {
        return $this->resource;
    }

    /**
     * Swap the current resource with another one.
     *
     * @param mixed $resource
     * @access public
     * @throws \InvalidArgumentException if the resource is not of type \Imagick.
     * @return void
     */
    public function swapResource($resource)
    {
        if (false === ($resource instanceof Imagick)) {
            throw new \InvalidArgumentException('Wrong resource type');
        }

        $this->resource = $resource;
    }

    /**
     * Returns the output type as file extension.
     *
     * Originally this is the source file type unless the output format was
     * explicitly defined.
     *
     * @access public
     * @return string
     */
    public function getOutputType()
    {
        return $this->outputType ?: $this->getExtensionFromMime($this->resource->getImageFormat());
    }

    /**
     * Applies a filter to the resource.
     *
     * @param string $name   the filter name.
     * @param array $options the filter options.
     * @access public
     * @return integer
     */
    public function filter($name, array $options = [])
    {
        $result = static::INT_FILTER;

        if ($this->isMultipartImage()) {
            $this->resource = $this->resource->coalesceImages();
        }

        //apply internal filters first.
        foreach ($this->resource as $frame) {
            $result = parent::filter($name, $options);
        }

        //look for external filters.
        if (static::EXT_FILTER === $result) {
            if (isset($this->filters[$name])) {
                $filter = new $this->filters[$name]($this, $options);
            } elseif (class_exists($filterClass = static::getFilterClassName($name))) {
                $filter = new $filterClass($this, $options);
            } else {
                return $result;
            }

            foreach ($this->resource as $frame) {
                $filter->run();
            }
        }

        return $result;
    }

    /**
     * Get the contents of the resource.
     *
     * @access public
     * @return string
     */
    public function getImageBlob()
    {
        if (!$this->processed) {
            return file_get_contents($this->source);
        }

        if ($this->isMultipartImage()) {

            $this->tmpFile = tempnam($this->tmp, 'jitim_');

            $image = $this->resource->deconstructImages();
            $image->writeImages($this->tmpFile, true);

            $image->clear();
            $image->destroy();

            return file_get_contents($this->tmpFile);

        }

        return $this->resource->getImageBlob();
    }

    /**
     * Set the backgroundcolor fot that image.
     *
     * @param mixed $color
     * @access public
     * @return mixed
     */
    public function setBackgroundColor($color)
    {
        $this->resource->setImageBackgroundColor($color);
    }

    /**
     * Set the compression quality fot that image.
     *
     * @param mixed $param
     * @access public
     * @return mixed
     */
    public function setQuality($quality)
    {
        $this->resource->setImageCompressionQuality($quality);
    }

    /**
     * Set the final output type on the resource.
     *
     * @param mixed $param
     * @access public
     * @return void
     */
    public function process()
    {
        if (null !== $this->outputType) {
            $this->resource->setImageFormat($this->outputType);
        }
        $this->processed = true;
    }

    /**
     * filterResizeToFit
     *
     * @access protected
     * @return void
     */
    protected function filterResizeToFit()
    {
        $this->resize($this->targetSize['width'], $this->targetSize['height'], static::FL_OSRK_LGR);
    }

    /**
     * gravity
     *
     * pretty useless compared to the cli version
     *
     * @param mixed $gravity
     * @param string $flag
     * @access protected
     * @return \Thapp\JitImage\Driver\ImagickDriver
     */
    protected function gravity($gravity, $flag = '')
    {
        unset($flag);

        $this->resource->setGravity($gravity);
        return $this;
    }

    /**
     * repage
     *
     * @access protected
     * @return mixed
     */
    protected function repage()
    {
        $this->resource->setImagePage(0, 0, 0, 0);
    }

    /**
     * background
     *
     * @param mixed $color
     * @access protected
     * @return \Thapp\JitImage\Driver\ImagickDriver
     */
    protected function background($color = null)
    {
        if (!is_null($color)) {
            $this->resource->setImageBackgroundColor(sprintf('#%s', $color));
        }
        return $this;
    }
    /**
     * extent
     *
     * @param mixed $width
     * @param mixed $height
     * @param string $flag
     * @access protected
     * @return \Thapp\JitImage\Driver\ImagickDriver
     */
    protected function extent($width, $height, $flag = '')
    {
        unset($flag);

        $coords = $this->getCropCoordinates(
            $this->resource->getImageWidth(),
            $this->resource->getImageHeight(),
            $width,
            $height,
            $this->resource->getGravity()
        );

        $this->resource->extentImage($width, $height, $coords['x'], $coords['y']);
        return $this;
    }

    /**
     * resize
     *
     * @param mixed  $width
     * @param mixed  $height
     * @param string $flag
     * @access protected
     * @return \Thapp\Image\Driver\ImagickDriver
     */
    protected function resize($width, $height, $flag = '')
    {
        $w = $this->getInfo('width');
        $h = $this->getInfo('height');

        switch ($flag) {
            // oversize image to fill the boudaries
            case static::FL_FILL_AREA:
                $this->fillArea($width, $height, $w, $h);
                break;
            // ignoring aspect ration is default behaviour on imagick resize
            case static::FL_IGNR_ASPR:
                break;
            case static::FL_PIXL_CLMT:
                extract($this->pixelLimit($w, $h, $width));
                break;
            case static::FL_RESZ_PERC:
                extract($this->percentualScale($w, $h, $width));
                break;
            // No scaling for larger images.
            // Would be easier to just set `bestfit`, but its behaviour changed
            // with imagemagick 3.0, so we have to calculate the best fit our selfs.
            case static::FL_OSRK_LGR:
                extract($this->fitInBounds($width, $height, $w, $h));
                break;
            // therefore we set $height always to zero
            default:
                $height = 0;
                break;
        }

        // filter and blur differ for up and downscaling
        if ($width > $w || $height > $h) {
            $filter = Imagick::FILTER_CUBIC;
            $blur   = 0.6;
        } else {
            $filter = Imagick::FILTER_SINC;
            $blur   = 1;
        }

        $this->resource->resizeImage($width, $height, $filter, $blur);
        return $this;
    }

    /**
     * getSourceAttributes
     *
     * @access protected
     * @return array
     */
    protected function getSourceAttributes()
    {
        extract($this->resource->getImageGeometry());

        return [
            'width'  => $width,
            'height' => $height,
            'ratio'  => $this->ratio($width, $height),
            'size'   => $this->resource->getImageLength(),
            'type'   => sprintf('image/%s', strtolower($this->resource->getImageFormat())),
        ];
    }

    /**
     * isMultipartImage
     *
     * @access protected
     * @return boolean
     */
    protected function isMultipartImage()
    {
        return $this->resource->getNumberImages() > 1;
    }


    /**
     * callParentFilter
     *
     * @access private
     * @return integer
     */
    private function callParentFilter()
    {
        return call_user_func_array([$this, 'Thapp\Image\Driver\AbstractDriver::filter'], func_get_args());
    }
}
