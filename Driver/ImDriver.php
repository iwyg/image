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

use \Symfony\Component\Process\Process;
use \Symfony\Component\Process\ProcessBuilder;
use \Thapp\Image\Driver\Loader\LoaderInterface;
use \Thapp\Image\Driver\Loader\FilesystemLoader;
use \Thapp\Image\Exception\ImageProcessException;

/**
 * Imagemagick Processing Driver
 *
 * @uses AbstractDriver
 *
 * @package Thapp\JitImage
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ImDriver extends AbstractDriver
{

    /**
     * driverType
     *
     * @var string
     */
    protected static $driverType = 'im';

    /**
     * source
     *
     * @var string
     */
    protected $source;

    /**
     * loader
     *
     * @var mixed
     */
    protected $loader;

    /**
     * loaded
     *
     * @var boolean
     */
    protected $loaded;

    /**
     * commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * a dictionary containing the intended width
     * and height values of the target file
     *
     * @var mixed
     */
    protected $targetSize = [];


    /**
     * path to temporary system directory
     *
     * @var string
     */
    protected $tmp;

    /**
     * temporary image file name
     *
     * @var string
     */
    protected $tmpFile;

    /**
     * tmpFiles
     *
     * @var array
     */
    protected $tmpFiles;

    /**
     * intermediate
     *
     * @var mixed
     */
    protected $intermediate;

    /**
     * imageFrames
     *
     * @var int
     */
    protected $imageFrames;

    /**
     * path to convert binary
     *
     * @var string
     */
    private $converter;

    /**
     * __construct
     *
     * @access public
     * @return mixed
     */
    public function __construct(LoaderInterface $loader = null, BinLocatorInterface $locator = null)
    {
        $this->tmp       = sys_get_temp_dir();
        $this->loader    = $loader ?: new FilesystemLoader;
        $locator = $locator ?: new ImBinLocator;
        $this->converter = $locator->getConverterPath();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Thapp\JitImage\Exception\ImageProcessException;
     */
    public function process()
    {
        $process = new Process($this->compile());
        $process->run();

        if (!$process->isSuccessFul()) {
            $this->clean();
            throw new ImageProcessException($process->getErrorOutput());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->loaded = false;
        $this->commands = [];
        $this->tmpFile = null;
        $this->loader->clean();

        parent::clean();
    }

    /**
     * cleanUpTmp
     *
     * @access protected
     * @return mixed
     */
    protected function cleanUpTmp()
    {
        if (empty($this->tmpFiles)) {
            return;
        }

        foreach ($this->tmpFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        $this->tmpFiles = [];
    }

    /**
     * filter
     *
     * @param mixed $name
     * @param mixed $options
     * @access public
     * @return boolean|void
     */
    public function filter($name, array $options = [])
    {
        if (static::EXT_FILTER !== ($result = parent::filter($name, $options))) {

            return $result;

        }

        if ($filter = $this->getExternaleFilter($name, $options)) {

            $filterCommands = $filter->run();

            if (!empty($filterCommands)) {
                $this->commands = array_merge($this->commands, (array)$filterCommands);
            }
        }

        return $result;
    }

    /**
     * getResource
     *
     * @access public
     * @return mixed
     */
    public function getResource()
    {
        throw new \LogicException(sprintf('%s has no resource', get_class($this)));
    }

    /**
     * getResource
     *
     * @access public
     * @return mixed
     */
    public function swapResource($resource)
    {
        throw new \LogicException(sprintf('%s has no resource', get_class($this)));
    }

    /**
     * setQuality
     *
     * @param mixed $quality
     * @access public
     * @return mixed
     */
    public function setQuality($quality)
    {
        $this->commands['-quality %d'] = [(int)$quality];
    }

    /**
     * {@inheritDoc}
     */
    public function getImageBlob()
    {
        return file_get_contents($this->tmpFile ?: $this->source);
    }

    /**
     * background
     *
     * @param string $color
     * @access protected
     * @return \Thapp\JitImage\Driver\ImagickDriver
     */
    protected function background($color = null)
    {
        if (is_string($color)) {
            $this->commands['-background "#%s"'] = [trim((string)$color, '#')];
        }
        return $this;
    }

    /**
     * resize
     *
     * @access protected
     * @return \Thapp\JitImage\Driver\ImDriver
     */
    protected function resize($width, $height, $flag = '')
    {
        $min = min($width, $height);
        $cmd = '-resize %sx%s%s';

        switch ($flag) {
            case static::FL_OSRK_LGR:
                break;
            case static::FL_RESZ_PERC:
                $cmd = '-resize %s%s%s';
                break;
            case static::FL_IGNR_ASPR:
            default:
                // compensating some imagick /im differences:
                if (0 === $width) {
                    $width = (int)floor($height * $this->getInfo('ratio'));
                }
                if (0 === $height) {
                    $height = (int)floor($width / $this->getInfo('ratio'));
                }
                $h = '';
                break;
        }

        $w = $this->getValueString($width);
        $h = $this->getValueString($height);

        $this->commands[$cmd] = [$w, $h, $flag];
        return $this;
    }

    /**
     * getMinTargetSize
     *
     * @param mixed $w
     * @param mixed $h
     * @access private
     * @return mixed
     */
    private function getMinTargetSize($w, $h)
    {
        extract($this->getInfo());

        if ($w > $width or $h > $height) {
            $w = $width;
            $h = $height;
        }

        if ($w > $h) {
            extract($this->getFilesize($w, 0));
        } else {
            extract($this->getFilesize(0, $h));
        }

        $this->targetSize = compact('width', 'height');
    }

    /**
     * extent
     *
     * @param mixed $width
     * @param mixed $height
     * @param string $flag
     * @access protected
     * @return \Thapp\JitImage\Driver\ImDriver
     */
    protected function extent($width, $height, $flag = '')
    {
        $this->commands['-extent %sx%s%s'] = [(string)$width, (string)$height, $flag];

        return $this;
    }

    /**
     * gravity
     *
     * @param mixed $gravity
     * @param string $flag
     * @access protected
     * @return \Thapp\JitImage\Driver\ImDriver
     */
    protected function gravity($gravity, $flag = '')
    {
        $this->commands['-gravity %s%s'] = [$this->getGravityValue($gravity), $flag];

        return $this;
    }

    /**
     * scale
     *
     * @param mixed $width
     * @param mixed $height
     * @param string $flag
     *
     * @access protected
     * @return \Thapp\JitImage\Driver\ImDriver
     */
    protected function scale($width, $height, $flag = '')
    {
        $this->commands['-scale %s%s%s'] = [$width, $height, $flag = ''];

        return $this;
    }

    /**
     * repage
     *
     * @access protected
     * @return \Thapp\JitImage\Driver\ImDriver
     */
    protected function repage()
    {
        $this->commands['%srepage'] = ['+'];

        return $this;
    }

    /**
     * getTempFile
     *
     * @access protected
     * @return string
     */
    protected function getTempFile($extenstion = null)
    {
        $extenstion = (null === $extenstion) ? '' : '.'.$extenstion;
        $this->tmpFiles[] = $file = tempnam($this->tmp, 'jitim_'.$extenstion);
        return $file;
    }

    /**
     * isMultipartImage
     *
     * @access protected
     * @return boolean
     */
    protected function isMultipartImage()
    {
        if (!is_int($this->imageFrames)) {

            $type = $this->getInfo('type');

            if ('image/gif' !== $type and 'image/png' !== $type) {

                $this->imageFrames = 1;

            } else {

                $identify = dirname($this->converter) . '/identify';
                $cmd = sprintf('%s -format %s %s', $identify, '%n', $this->source);

                $process = new Process($cmd);
                $process->run();

                if ($process->isSuccessFul()) {
                    $this->imageFrames = (int)$process->getOutput();
                } else {
                    throw new \RuntimeException($process->getErrorOutput());
                }
            }
        }
        return $this->imageFrames > 1;
    }

    /**
     * compile the convert command
     *
     * @access protected
     * @return string the compiled command
     */
    private function compile()
    {
        $origSource = $this->source;
        $type = preg_replace('#^image/#', null, $this->getInfo('type'));
        $in = sprintf('%s:%s', $type, escapeshellarg($this->source));

        $this->tmpFile = $tmpFile = $this->getTempFile();

        $args = [];

        if ($this->isMultipartImage()) {

            $intermediate = $this->getTempFile($type);
            $args[] = sprintf(
                '%s %s:%s -coalesce %s %s',
                $this->converter,
                $type,
                escapeshellarg($this->source),
                escapeshellarg($intermediate),
                PHP_EOL
            );
            $args[] = $this->converter;
            $args[] = $intermediate;
        } else {
            $args[] = $this->converter;
            $args[] = $in;
        }

        foreach ($this->commands as $command => $arg) {
            $args[] = vsprintf($command, $arg);
        }

        $args[] = sprintf('%s:%s', $this->getOutputType(), escapeshellarg($tmpFile));
        $cmd = escapeshellcmd(implode(' ', $args));

        $repl = "\\\\" . implode("|\\\\", ['#', PHP_EOL]);
        $cmd = preg_replace_callback(
            "~$repl~",
            function ($found) {
                return trim($found[0], "\\");
            },
            $cmd
        );
        return $cmd;
    }

    /**
     * getGravityValue
     *
     * @param mixed $gravity
     * @access protected
     * @return string
     */
    protected function getGravityValue($gravity)
    {
        switch ($gravity) {
            case 1:
                return 'northwest';
            case 2:
                return 'north';
            case 3:
                return 'northeast';
            case 4:
                return 'west';
            case 5:
                return 'center';
            case 6:
                return 'east';
            case 7:
                return 'southwest';
            case 8:
                return 'south';
            case 9:
                return 'southeast';
            default:
                return 'center';
        }
    }

    /**
     * convert zero integers to an empty string.
     *
     * @param mixed $value
     * @access private
     * @return string
     */
    private function getValueString($value)
    {
        return (string)(0 === $value ? '' : $value);
    }
}
