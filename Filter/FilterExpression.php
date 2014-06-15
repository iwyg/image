<?php

/**
 * This File is part of the Thapp\Image\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

/**
 * @class FilterExpression
 * @package Thapp\Image\Filter
 * @version $Id$
 */
class FilterExpression
{
    /**
     * expr
     *
     * @var string
     */
    private $expr;

    /**
     * params
     *
     * @var array
     */
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = $this->compile();

        return $str;
    }

    public function __clone()
    {
        $this->expr = null;
        $this->params = null;
    }

    private function ensureArray()
    {
        if (!is_array($this->params)) {
            $this->toArray();
        }
    }

    public function addFilter($filter, array $options = [])
    {
        if (0 === strlen($filter)) {
            return;
        }

        $this->ensureArray();
        $this->params[$filter] = $options;
    }

    /**
     * Compile this expression to string
     *
     * @return string
     */
    public function compile()
    {
        if ($this->expr) {
            return $this->expr;
        }

        if (is_string($this->params)) {
            return $this->expr = $this->params;
        }

        return $this->expr = $this->compileParams();
    }

    /**
     * Transform this expression to an array
     *
     * @return array
     */
    public function toArray()
    {
        if (is_array($this->params)) {
            return $this->params;
        }

        if (0 === strlen($this->params)) {
            return $this->params = [];
        }

        return $this->transFormString($this->params);
    }

    /**
     * Parse the input string expression
     *
     * @param string $str
     *
     * @return array
     */
    private function transformString($str)
    {
        $filters = [];

        foreach (preg_split('~:~', $str, -1, PREG_SPLIT_NO_EMPTY) as $filter) {

            if (0 === substr_count($filter, ';')) {
                $filters[$filter] = [];
                continue;
            }

            list ($fname, $options) = explode(';', $filter);

            $opt = [];

            foreach (explode(',', $options) as $option) {
                list ($oname, $val) = $this->getOption($option);

                $opt[$oname] = $val;
            }

            $filters[$fname] = $opt;
        }

        return $this->params = $filters;
    }

    private function getOption($option)
    {
        if (0 === substr_count($option, '=')) {
            $oname = $option;
            $val = null;
        } else {
            list ($oname, $val) = explode('=', $option);
        }

        return [$oname, $this->getOptionValue($val)];
    }

    /**
     * @return mixed
     */
    private function getOptionValue($val)
    {
        if (!is_string($val)) {
            return $val;
        }

        switch (true) {
            case 0 === strlen($val) || 'null' === $val:
                return null;
            case is_numeric($val):
                return 0 !== substr_count($val, '.') ?
                    (float)$val : (0 === strpos($val, '0x') ? hexdec($val)  : (int)$val);
            case in_array($val, ['true', 'false']):
                return 'true' === $val ? true : false;
            default:
                return $val;
        }
    }

    /**
     * @return string
     */
    private function compileParams()
    {
        $filters = [];

        foreach ($this->params as $fname => $options) {

            if (is_int($fname)) {
                $fname   = $options;
                $options = [];
            }

            array_push($filters, ':', $fname);

            $opts = [];

            if (empty($options)) {
                continue;
            }

            foreach ((array)$options as $key => $value) {
                $opts[] = sprintf('%s=%s', $key, $value);
            }

            $filters[] = ';' . implode(',', $opts);
        }

        array_shift($filters);

        return implode('', $filters);
    }
}
