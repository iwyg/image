<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image;

/**
 * @class Source
 * @package Thapp\Image
 * @version $Id$
 */
class Source implements SourceInterface
{
    protected $name;

    protected $path;

    protected $content;

    /**
     * __construct
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function __construct($file)
    {
        $this->setPath($file);
        $this->setName(pathinfo($file, PATHINFO_BASENAME));
        $this->setContent(file_get_contents($file));
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
