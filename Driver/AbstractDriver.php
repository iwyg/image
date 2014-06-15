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

use \Thapp\Image\Traits\Scaling;

/**
 * Abstract processing driver
 *
 * @implements DriverInterface
 * @abstract
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class AbstractDriver implements DriverInterface
{
    use Scaling;

    /**
     * loaded
     *
     * @var boolean
     */
    protected $loaded;

    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * tmpFile
     *
     * @var string
     */
    protected $tmpFile;

    /**
     * filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * targetSize
     *
     * @var array
     */
    protected $targetSize = [];

    /**
     * sourceAttributes
     *
     * @var array
     */
    protected $sourceAttributes;

    /**
     * outputType
     *
     * @var mixed
     */
    protected $outputType;

    /**
     * processed
     *
     * @var bool
     */
    protected $processed = false;

    /**
     * Loads an image source.
     *
     * @param mixed $source
     *
     * @access public
     * @return boolean
     */
    public function load($source)
    {
        $this->clean();

        //if (!$this->loader->supports($source)) {
        //    throw new \InvalidArgumentException(sprintf('resource "%s" not found or not supported', $source));
        //}

        if ($this->loaded = (bool)($src = $this->loader->load($source))) {
            $this->source = $src;

            return $this->loaded;
        }

        throw new \RuntimeException(sprintf('error loading source "%s"', $source));
    }

    /**
     * Call a filter on the driver.
     *
     * if the filter method exists on the driver the method will be called,
     * otherwise it will return a flag to indecate that the filter is an
     * external one.
     *
     * @param string $name
     * @param array  $options
     * @access public
     * @return int
     */
    public function filter($name, array $options = [])
    {
        if (method_exists($this, $filter = 'filter' . ucfirst($name))) {
            call_user_func_array([$this, $filter], is_array($options) ? $options : []);

            return static::INT_FILTER;
        }

        return static::EXT_FILTER;
    }

    /**
     * getExternalFilter
     *
     * @param mixed $name
     *
     * @access protected
     * @return mixed
     */
    protected function getExternalFilter($name, $options)
    {
        if (isset($this->filters[$name])) {
            return new $this->filters[$name]($this, $options);
        }

        if (class_exists($filterClass = static::getFilterClassName($name))) {
            return new $filterClass($this, $options);
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $this->processed = true;
    }

    /**
     * Determine if an image has been processed yet.
     *
     * @throws \InvalidArgumentException if the source is not supported by the
     * sourceloader
     * @throws \RuntimeException if the source could not be loaded
     * @access public
     * @return bool
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $this->source           = null;
        $this->resource         = null;
        $this->processed        = false;
        $this->targetSize       = null;
        $this->outputType       = null;
        $this->sourceAttributes = null;

        $this->cleanUpTmp();
        $this->loader->clean();
    }

    /**
     * Clean up temporary files after shutdown.
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->clean();
    }

    /**
     * register external filter.
     *
     * @param string $alias the filter alias
     * @param string $class full qualified filter classname
     *
     * @access public
     * @return void
     */
    public function registerFilter($alias, $class)
    {
        $this->filters[$alias] = $class;
    }

    /**
     * Retrurns the driver type name.
     *
     * @access public
     * @final
     * @return string
     */
    final public function getDriverType()
    {
        return static::$driverType;
    }

    /**
     * Set the image target size.
     *
     * @param int $width
     * @param int $height
     * @access public
     * @return void
     */
    public function setTargetSize($width, $height)
    {
        $this->targetSize = compact('width', 'height');
    }

    /**
     * Get the target width and height of the image.
     *
     * @access public
     * @return array containing int $width and int $height of the image
     */
    public function getTargetSize()
    {
        extract($this->targetSize);

        return $this->getImageSize((int)$width, (int)$height);
    }

    /**
     * getInfo
     *
     * @param mixed $attribute
     * @access public
     * @return mixed
     */
    public function getInfo($attribute = null)
    {
        if (null === $this->sourceAttributes) {
            $this->sourceAttributes = $this->getSourceAttributes();
        }

        if (null !== ($attribute)) {
            return isset($this->sourceAttributes[$attribute]) ? $this->sourceAttributes[$attribute] : null;
        }

        return $this->sourceAttributes;
    }

    /**
     * Get the image source originally loaded into the driver.
     *
     * @access public
     * @return string
     */
    public function getSource()
    {
        return $this->loader->getSource();
    }

    /**
     * setOutputType
     *
     * @param mixed $type
     * @access public
     * @return void
     */
    public function setOutputType($type)
    {
        if (preg_match('/(png|gif|jpe?g|tif?f|webp)/i', $type)) {
            $this->outputType = strtr(strtolower($type), ['jpeg' => 'jpg']);
            return;
        }

        throw new \InvalidArgumentException(sprintf('Invalid output format %s', $type));
    }

    /**
     * getSourceFormat
     *
     * @param mixed $assSuffix
     *
     * @access public
     * @return string
     */
    public function getSourceType($assSuffix = false)
    {
        $type = $this->getInfo('type');

        return (bool)$assSuffix ? $this->getExtensionFromMime($type) : $type;
    }

    /**
     * getOutputType
     *
     * @access public
     * @return string
     */
    public function getOutputType()
    {
        $type = $this->outputType;

        if (null === $type) {
            $type = $this->getInfo('type');
        }

        return $this->getExtensionFromMime($type);
    }

    /**
     * cleanUpTmp
     *
     * @access protected
     * @abstract
     * @return mixed
     */
    abstract protected function cleanUpTmp();

    /**
     * filterResize
     *
     * @param mixed $param
     *
     * @access protected
     * @return void
     */
    protected function filterResize()
    {
        $this->resize($this->targetSize['width'], $this->targetSize['height'], static::FL_IGNR_ASPR);
    }

    /**
     * Crop and resize filter.
     *
     * @param int $width
     * @param int $height
     * @param int $gravity
     *
     * @access protected
     * @return void
     */
    protected function filterCropScale($gravity)
    {
        $this
            ->resize($this->targetSize['width'], $this->targetSize['height'], static::FL_FILL_AREA)
            ->gravity($gravity)
            ->extent($this->targetSize['width'], $this->targetSize['height']);

    }

    /**
     * Crop filter.
     *
     * @param int $with
     * @param int $height
     * @param int $gravity
     *
     * @access protected
     * @return void
     */
    protected function filterCrop($gravity, $background = null)
    {
        $this
            ->background($background)
            ->gravity($gravity)
            ->extent($this->targetSize['width'], $this->targetSize['height']);
    }

    /**
     * Best fit filter.
     *
     * @access protected
     * @return void
     */
    protected function filterResizeToFit()
    {
        $this->resize($this->targetSize['width'], $this->targetSize['height'], static::FL_OSRK_LGR);
    }

    /**
     * Percentual resize filter.
     *
     * @access protected
     * @return void
     */
    protected function filterPercentualScale()
    {
        $this->resize($this->targetSize['width'], 0, static::FL_RESZ_PERC);
    }

    /**
     * filterPercentualScale
     *
     * @access protected
     * @return void
     */
    protected function filterResizePixelCount()
    {
        $this->resize($this->targetSize['width'], 0, static::FL_PIXL_CLMT);
    }

    /**
     * Resize the image.
     *
     * @param int    $width
     * @param int    $height
     * @param string $flag
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function resize($width, $height, $flag = '');

    /**
     * Set the image gravity.
     *
     * @param int    $gravity
     * @param string $flag
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function gravity($gravity, $flag = '');

    /**
     * Extent the image.
     *
     * @param int    $width
     * @param int    $height
     * @param string $flag
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function extent($width, $height, $flag = '');

    /**
     * Set the image background.
     *
     * @param string $color hex color representation.
     *
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function background($color = null);

    /**
     * getFilesizeFromCommand
     *
     * @param mixed $width
     * @param mixed $height
     * @access private
     * @return array
     */
    protected function getImageSize($width, $height)
    {
        $min = min((int)$width, (int)$height);

        // if one value is zero, we have to calculate the right
        // value using the image aspect ratio
        if (0 === $min) {

            // if both hight and widh are zero we assume
            // that the image is not resized at all
            if (0 === max($width, $height)) {
                extract($this->getInfo());
            } else {
                $ratio = $this->getInfo('ratio');
            }

            $width  = 0 === $width  ? (int) floor($height * $ratio) : $width;
            $height = 0 === $height ? (int) floor($width  / $ratio) : $height;
        }

        return compact('width', 'height');
    }

    /**
     * formatType
     *
     * @param mixed $type
     *
     * @access protected
     * @return mixed
     */
    protected function formatType($type)
    {
        return strtr(strtolower($type), ['jpeg' => 'jpg']);
    }

    /**
     * getExtensionFromMime
     *
     * @param mixed $mime
     *
     * @access protected
     * @return string
     */
    protected function getExtensionFromMime($mime)
    {
        return preg_replace('~image/~', null, $this->formatType($mime));
    }

    /**
     * getOutputMimeType
     *
     * @access public
     * @return mixed
     */
    public function getOutputMimeType()
    {
        return image_type_to_mime_type($this->getImageTypeConstant($this->getOutputType()));
    }

    /**
     * getSourceAttributes
     *
     * @access protected
     * @return array
     */
    protected function getSourceAttributes()
    {
        list($width, $height) = $info = getimagesize($this->source);

        return [
            'width'    => $width,
            'height'   => $height,
            'ratio'    => $this->ratio($width, $height),
            'size'     => filesize($this->source),
            'type'     => $info['mime']
        ];
    }

    /**
     * getImageTypeConstant
     *
     * @param mixed $type
     * @access private
     * @return int
     */
    private function getImageTypeConstant($type)
    {
        switch ($type) {
            case 'jpg':
                return IMAGETYPE_JPEG;
            case 'jpeg':
                return IMAGETYPE_JPEG;
            case 'gif':
                return IMAGETYPE_GIF;
            case 'png':
                return IMAGETYPE_PNG;
            case 'webp':
                return IMAGETYPE_WBMP;
            case 'webp':
                return IMAGETYPE_WBMP;
            case 'ico':
                return IMAGETYPE_ICO;
            case 'bmp':
                return IMAGETYPE_BMP;
            default:
                return IMAGETYPE_JPC;
        }
    }

    protected static function getFilterClassName($name)
    {
        $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $type = ucfirst(strtolower(static::$driverType));

        return implode('\\', ['Thapp\Image\Filter', $name, $type . $name. 'Filter']);
    }
}
