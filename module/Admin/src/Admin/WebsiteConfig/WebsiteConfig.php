<?php 
/**
 * Website config editor
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\WebsiteConfig;

use Zend\Config\Writer\PhpArray as PhpArrayWriter;
use Zend\Config\Config;

class WebsiteConfig 
{
    const CONFIG_DIR = 'config/autoload';
    const CONFIG_EXT = 'php';

    /**
     * @var array
     */
    protected $cached;

    /**
     * @param string $namespace
     * @param Config $config
     */
    public function save($namespace, Config $config)
    {
        $namespace = strtolower($namespace);
        $splitted  = explode('/', strtolower($namespace));
        $dir       = self::CONFIG_DIR;

        if (count($splitted) < 2) {
            throw new \Exception(
                sprintf('Invalid config namespace \'%s\'', $namespace)
            );
        }

        $writer    = new PhpArrayWriter();
        $filename  = sprintf(
            '%s%s%s.%s.%s', 
            $dir,
            DIRECTORY_SEPARATOR,
            $splitted[1], 
            $splitted[0], 
            self::CONFIG_EXT
        );

        $this->cached[$filename] = $config;

        return $writer->toFile($filename, $config);
    }

    /**
     * @param string $namespace
     */
    public function get($namespace) 
    {
        $dir = rtrim(self::CONFIG_DIR, '/\\') . DIRECTORY_SEPARATOR;

        $splitted = explode('/', strtolower($namespace));

        if (!$splitted || count($splitted) == 1) {
            throw new \Exception(
                sprintf("Undefined namespace '%s'", $namespace)
            );            
        }

        $filename = sprintf(
            '%s%s.%s.%s', 
            $dir,
            $splitted[1], 
            $splitted[0], 
            self::CONFIG_EXT
        );

        if (!is_file($filename)) {
            throw new \Exception(
                sprintf("Undefined namespace '%s'", $namespace)
            );
        }

        if (!isset($this->cached[$filename])) {
            $this->cached[$filename] = new Config(include $filename, true);
        }

        return $this->cached[$filename];
    }
}