<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

use Thapp\Image\Info\ImageReader;
use Thapp\Image\Info\MetaDataReaderInterface;

/**
 * @class AbstractSource
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractSource implements SourceInterface
{
    protected $reader;

    /**
     * Constructor.
     *
     * @param MetaDataReaderInterface $reader
     */
    public function __construct(MetaDataReaderInterface $reader = null)
    {
        $this->reader = $reader ?: new ImageReader;
    }

    /**
     * getReader
     *
     * @return MetaDataReaderInterface
     */
    protected function getReader()
    {
        if (null === $this->reader) {
            $this->reader = new ImageReader;
        }

        return $this->reader;
    }
}
