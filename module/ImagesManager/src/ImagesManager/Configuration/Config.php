<?php 
/**
 * Configuration for images manager
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Configuration;

use Zend\Config\Config as ZendConfig;
use Zend\Config\Writer\PhpArray as PhpWriter;

class Config
{
    /**
     * @var string $filepath
     */
    protected $filepath;

    /**
     * @var bool $readonly
     */
    protected $readonly;

    /**
     * @var PhpWriter $writer
     */
    protected $writer;

    /**
     * @var ZendConfig $config
     */
    protected $config;

    /**
     * Construct inits class with given phparray config file
     *
     * @param string $filepath config file path
     * @param bool $readonly default is true
     */
    public function __construct($filepath, $readonly = true)
    {
        $config = [];

        if (is_file($filepath) 
            && is_array(($config = include $filepath))
        ) {
            $this->filepath = realpath($filepath);
            $this->readonly = $readonly;

            if (!$this->readonly) {
                $this->writer   = new PhpWriter();
            }

            $this->config = new ZendConfig($config, !$readonly);
        } else {
            throw new Exception\InvalidConfigFilepathException();
        }
    }

    /**
     * If class not initiated as readonly writes changes to filepath
     */
    public function __destruct()
    {
        if (!$this->readonly) {
            // Write current state to file
            $this->writer->toFile($this->filepath, $this->config);
        }
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getConfig()->get($name);
    }

    /**
     * Set a value in the config.
     *
     * Only allow setting of a property if $readonly was set to false
     * on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return void
     * @throws Exception\RuntimeException
     */
    public function __set($name, $value)
    {
        if (!$this->isReadOnly()) {
            $this->getConfig()->__set($name, $value);
        } else {
            throw new Exception\RuntimeException('Config is read only');
        }
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getConfig()->toArray();
    }

    /**
     * Is readonly
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readonly;
    }

    /**
     * Returns config class
     *
     * @return ZendConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns filepath
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }
}