<?php

/*
 * This File is part of the Thapp\Image\Color\Profile package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color\Profile;

/**
 * @class Profile
 *
 * @package Thapp\Image\Color\Profile
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Profile implements ProfileInterface
{
    private $name;
    private $file;
    private $content;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $file
     */
    public function __construct($name, $file)
    {
        $this->name = $name;
        $this->setfile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        if (null === $this->content && !$this->content = @file_get_contents($this->file)) {
            throw new \RuntimeException;
        }

        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * fromString
     *
     * @param mixed $name
     * @param mixed $profile
     *
     * @return Profile
     */
    public static function fromString($name, $profile)
    {
        $path = sprintf('data://text/plain;base64,%s', base64_encode($profile));

        return new static($name, $path);
    }

    /**
     * setFile
     *
     * @param mixed $file
     * @throws \RuntimeException
     *
     * @return void
     */
    private function setFile($file)
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException();
        }

        if ((!is_file($file) || !stream_is_local($file)) && 0 !== mb_strpos($file, 'data://', 0, '8bit')) {
            throw new \RuntimeException('Cannot read profile.');
        }

        $this->file = $file;
    }
}