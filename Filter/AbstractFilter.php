<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

use Thapp\Image\Driver\DriverInterface;
use Thapp\Image\Filter\Traits\FilterHelperTrait;

/**
 * @abstract class AbstractFilter implements FilterInterface
 * @see FilterInterface
 * @abstract
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractFilter implements FilterInterface
{
    use FilterHelperTrait;

    /**
     * driver
     *
     * @var mixed
     */
    protected $driver;

    /**
     * options
     *
     * @var array
     */
    protected $options;

    protected $availableOptions = [];

    protected $optionAliases = [];

    /**
     * Exeecute the filter processing.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Creates a new filter object.
     *
     * @param Imagick $resource
     *
     * @return void
     */
    final public function __construct(DriverInterface $driver, $options)
    {
        $this->driver  = $driver;
        $this->setOptions($options);

        $this->ensureCompat();
    }

    /**
     * Get a filter option.
     *
     * @param string $option option name
     * @param mixed  $default the default value to return
     *
     * @return mixed
     */
    public function getOption($option, $default = null)
    {
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }
        return $default;
    }

    /**
     * getOptionAlias
     *
     * @param string $alias
     *
     * @return string
     */
    protected function getOptionAlias($alias)
    {
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @access protected
     * @return void
     */
    protected function setOptions(array $options)
    {
        $this->options = [];

        foreach ($options as $option => $value) {

            if (!in_array($option, (array)$this->availableOptions)) {
                throw new \InvalidArgumentException(
                    sprintf('filter %s has no option "%s"', get_class($this), $option)
                );
            }

            $this->options[$option] = $value;
        }
    }

    /**
     * Ensure driver compatibility.
     *
     * @throws \Exception
     * @return void
     */
    private function ensureCompat()
    {
        if (!static::$driverType) {
            throw new \Exception(
                sprintf(
                    'trying to apply incopatible filter on %s driver',
                    $this->driver->getDriverType()
                )
            );
        }
    }
}
