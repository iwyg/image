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
use \Thapp\Image\Driver\Loader\LoaderInterface;

/**
 * GD Processing Driver
 *
 * @implements DriverInterface
 *
 * @package Thapp\Image
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class GdDriver extends AbstractDriver
{
    use Scaling;

    /**
     * resource
     *
     * @var mixed
     */
    protected $resource;

    /**
     * source
     *
     * @var mixed
     */
    protected $source;

    /**
     * gravity
     *
     * @var mixed
     */
    protected $gravity;

    /**
     * outputType
     *
     * @var string
     */
    protected $outputType;

    /**
     * quality
     *
     * @var int
     */
    protected $quality;

    /**
     * background
     *
     * @var bool|integer
     */
    private $background;

    protected static $driverType = 'gd';

    /**
     * __construct
     *
     * @param SourceLoaderInterface $loader
     * @access public
     * @return mixed
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function load($source)
    {
        $this->clean();

        if (!$src = $this->loadResourceFromType($source)) {
            return false;
        }

        $this->resource = $src;
        return true;
    }

    /**
     * setQuality
     *
     * @param mixed $quality
     *
     * @access public
     * @return void
     */
    public function setQuality($quality)
    {
        $this->quality = (int)$quality;
    }

    /**
     * cleanUpTmp
     *
     * @access protected
     * @return void
     */
    protected function cleanUpTmp()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getImageBlob()
    {
        if (!$this->isProcessed()) {
            return file_get_contents($this->getSource());
        }

        $fn = sprintf('image%s', $type = strtr($this->getOutputType(), ['jpg' => 'jpeg']));

        if ('png' === $type) {
            imagesavealpha($this->getResource(), true);
        }

        ob_start();

        call_user_func($fn, $this->resource, null, $this->getQuality());
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * filter
     *
     * @param mixed $name
     * @param mixed $options
     * @access public
     * @return void
     */
    public function filter($name, array $options = [])
    {
        if (static::EXT_FILTER !== ($result = parent::filter($name, $options))) {
            return $result;
        }

        if ($filter = $this->getExternaleFilter($name, $options)) {
            $filter->run();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        if (is_resource($this->resource)) {
            imagedestroy($this->resource);
        }

        parent::clean();
    }

    /**
     * getResource
     *
     * @param mixed $resource
     * @access public
     * @return void
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * swapResource
     *
     * @param mixed $resource
     * @access public
     * @return void
     */
    public function swapResource($resource)
    {
        if (!is_resource($resource) or 'gd' !== get_resource_type($resource)) {
            throw new \InvalidArgumentException('No resource given or wrong resource type');
        }

        $this->resource = $resource;
    }

    /**
     * post process background color if any
     *
     * {@inheritDoc}
     *
     */
    public function process()
    {
        parent::process();

        if (is_int($this->background)) {

            extract($this->getBackgroundCoordinates($this->getGravity()));
            imagefill($this->resource, $x1, $y1, $this->background);
            imagefill($this->resource, $x2, $y2, $this->background);

        }
    }

    /**
     * resize
     *
     * @param mixed $width
     * @param mixed $height
     * @param mixed $flag
     * @access protected
     * @return mixed
     */
    protected function resize($width, $height, $flag = null)
    {

        if (0 === min($width, $height)) {
            extract($this->getTargetSize());
        }

        switch ($flag) {
            case static::FL_IGNR_ASPR:
                break;
            // oversize image to fill the boundaries.
            case static::FL_FILL_AREA:
                $this->fillArea($width, $height, $this->getInfo('width'), $this->getInfo('height'));
                break;
            // No scaling for larger images.
            // Would be easier to just set `bestfit`, but its behaviour changed
            // with imagemagick 3.0, so we have to calculate the best fit ou selfs.
            case static::FL_OSRK_LGR:
                extract($this->fitInBounds($width, $height, $this->getInfo('width'), $this->getInfo('height')));
                break;
            case static::FL_RESZ_PERC:
                extract($this->percentualScale($this->getInfo('width'), $this->getInfo('height'), $width));
                break;
            case static::FL_PIXL_CLMT:
                extract($this->pixelLimit($this->getInfo('width'), $this->getInfo('height'), $width));
                break;
            // default set the appropiate height and take width as a fixure in case
            // both the image ratio and the resize ratio don't match
            default:
                $r1 = $this->getInfo('ratio');
                $r2 = $this->ratio($width, $height);

                if (0.001 < abs($r1 - $r2)) {
                    extract($this->getImageSize($width, 0));
                }
                break;
        }


        $resized = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $resized,
            $this->resource,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->getInfo('width'),
            $this->getInfo('height')
        );

        $this->swapResource($resized);
        return $this;
    }

    /**
     * gravity
     *
     * @param mixed $gravity
     * @access protected
     * @return mixed
     */
    protected function gravity($gravity, $flag = '')
    {
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * background
     *
     * @param mixed $color
     * @access protected
     * @return mixed
     */
    protected function background($color = null)
    {
        if (!is_null($color)) {

            $this->background = $this->getColorID($color);
            extract($this->getBackgroundCoordinates($this->getGravity()));

            imagefill($this->resource, $x1, $y1, $this->background);
            imagefill($this->resource, $x2, $x2, $this->background);
        }

        return $this;
    }

    /**
     * extent
     *
     * @param mixed $width
     * @param mixed $height
     * @param mixed $flag
     * @access protected
     * @return mixed
     */
    protected function extent($width, $height, $flag = null)
    {
        $w = $this->getInfo('width');
        $h = $this->getInfo('height');

        extract(
            $this->getCropCoordinates(
                $nw = imagesx($this->resource),
                $nh = imagesy($this->resource),
                $width,
                $height,
                $this->getGravity()
            )
        );

        $extent = imagecreatetruecolor($width, $height);

        imagecopy($extent, $this->resource, 0, 0, $x, $y, $width, $height);
        $this->swapResource($extent);

        return $this;
    }

    /**
     * get the current image gravity, defaults to 1
     *
     * @access protected
     * @return int the gravity value [1 - 9]
     */
    protected function getGravity()
    {
        return null === $this->gravity ? 1 : $this->gravity;
    }

    /**
     * getQuality
     *
     * @access protected
     * @return mixed
     */
    private function getQuality()
    {
        if ('png' === $this->getOutputType()) {
            return floor((9 / 100) * min(100, $this->quality));
        }
        return $this->quality;
    }

    /**
     * loadResourceFromType
     *
     * @param mixed $mime
     * @param mixed $source
     * @access private
     * @return mixed
     */
    private function loadResourceFromType($source)
    {

        $mime = null;
        extract(getimagesize($source));

        $fn = sprintf('imagecreatefrom%s', $type = substr($mime, strpos($mime, '/') + 1));

        if (!function_exists($fn)) {
            $this->error = sprintf('%s is not a supported image type', $mime);
            $this->clean();

            throw new \InvalidArgumentException(sprintf('Unsupported image format %s', $mime));
        }

        $this->source = $this->loader->load($source);
        $this->outputType = $mime;

        return call_user_func($fn, $this->source);
    }

    /**
     * getColorID
     *
     * @param mixed $color
     * @access private
     * @return int|boolean color id or false on failure
     */
    private function getColorID($color)
    {
        list ($r, $g, $b) = explode(
            ' ',
            implode(' ', str_split(strtoupper(3 === strlen($color) ? $color . $color : $color), 2))
        );

        return imagecolorallocate($this->resource, hexdec($r), hexdec($g), hexdec($b));
    }

    /**
     * getBackgroundCoordinates
     *
     * @param mixed $gravity
     * @access private
     * @return array
     */
    private function getBackgroundCoordinates($gravity)
    {
        $w = imagesx($this->resource);
        $h = imagesy($this->resource);

        $x1 = $y1 = 0;
        $x2 = $w - 1;
        $y2 = $h - 1;

        switch ($gravity) {
            case 1:
                $x1 = $w - 1;
                $y1 = $h - 1;
                $y2 = 0;
                break;
            case 5:
                $x1 = $w - 1;
                $y2 = $w - 1;
                $x2 = 0;
                $y2 = 0;
                break;
            case 9:
                break;
            default:
                $x2 = 0;
                $y2 = 0;
                break;
        }
        return compact('x1', 'y1', 'x2', 'y2');
    }
}
