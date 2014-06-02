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

/**
 * @interface DriverInterface
 * @package Thapp\Image
 * @version $Id$
 */
interface DriverInterface
{

    /**
     * Ignore aspect ration flag
     *
     * @var string
     */
    const FL_IGNR_ASPR = '!';

    /**
     * Fill area flag
     *
     * @var string
     */
    const FL_FILL_AREA = '^';

    /**
     * procentual resize flag
     *
     * @var string
     */
    const FL_RESZ_PERC = '%';

    /**
     * pixel count limit flag
     *
     * @var string
     */
    const FL_PIXL_CLMT = '@';

    /**
     * only resize smaller flag
     *
     * @var string
     */
    const FL_OENL_SML  = '<';

    /**
     * only resize larger flag
     *
     * @var string
     */
    const FL_OSRK_LGR  = '>';

    /**
     * @var int
     */
    const INT_FILTER = 1;

    /**
     * @var int
     */
    const EXT_FILTER = 2;

    /**
     * load the imagefile resource
     *
     * @param  string $source
     * @access public
     * @return void
     */
    public function load($source);

    /**
     * apply an imagefilter
     *
     * @param string $name    the name of the filter
     * @param array  $options filter options
     *
     * @access public
     * @return boolean|void
     */
    public function filter($name, array $options = []);

    /**
     * process the image
     *
     * This sould be called after the filters
     * have been applied.
     * Some drivers need this
     * to work properly (e.g `im`).
     *
     * @access public
     * @return void
     */
    public function process();

    /**
     *
     * Clean up resources
     *
     * This should be called after the image was process.
     * In some cases this will clean temporary files in
     * `/var/tmp`
     *
     * @access public
     * @return void
     */
    public function clean();

    /**
     * registers an imagefilter
     *
     * @param  string $alias filter alias
     * @param  string $class full qualified classname of the filter
     * @access public
     * @return void
     */
    public function registerFilter($alias, $class);

    /**
     * setOutPutType
     *
     * @param mixed $type
     * @access public
     * @return void
     */
    public function setOutputType($type);

    /**
     * setQuality
     *
     * @param mixed $quality
     *
     * @access public
     * @return void
     */
    public function setQuality($quality);

    /**
     * getSoruceType
     *
     * @param bool $assSuffix
     *
     * @access public
     * @return string
     */
    public function getSourceType($assSuffix = false);

    /**
     * get the file contents of the image
     *
     * @access public
     * @return string
     */
    public function getImageBlob();

    /**
     * getTargetSize
     *
     * @access public
     * @return array with width and height values
     */
    public function getTargetSize();

    /**
     * getResource
     *
     * @access public
     * @return void
     */
    public function getResource();

    /**
     * swapResource
     *
     * @param mixed $resource
     * @access public
     * @return void
     */
    public function swapResource($resource);

    /**
     * getDriverType
     *
     * @access public
     * @return string
     */
    public function getDriverType();

    /**
     * isProcessed
     *
     * @access public
     * @return bool
     */
    public function isProcessed();
}
