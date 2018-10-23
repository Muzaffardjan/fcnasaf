<?php 
/**
 * Template positions Config
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\WebsiteConfig;

use Zend\Config\Config;

class TemplatePositionsConfig
{
    const CONFIG_NAMESPACE = 'Global/Positions';
    const CONFIG_ROOT_KEY  = 'template_positions'; 

    /**
     * @var WebsiteConfig
     */
    protected $configWebsite;

    public function __construct(WebsiteConfig $configWebsite)
    {
        $this->configWebsite = $configWebsite;
        $this->positions     = $configWebsite->get(self::CONFIG_NAMESPACE);
    }

    /**
     * Saves config
     *
     * @return void
     */
    public function save()
    {
        return $this->configWebsite->save(
            self::CONFIG_NAMESPACE, 
            $this->positions
        );
    }

    /**
     * @param   null|string $key
     * @return  Config
     */
    public function get($key = null)
    {
        if (null === $key) {
            return $this->positions[self::CONFIG_ROOT_KEY];
        } elseif (gettype($key) !== 'object') {
            $depth  = explode('/', strtolower($key));
            $config = $this->positions[self::CONFIG_ROOT_KEY];

            foreach ($depth as $key) {
                if ($config->offsetExists($key)) {
                    $config = $config->offsetGet($key);
                } else {
                    return null;
                }
            }

            return $config;
        }
    }

    /**
     * @param   string    $key
     * @param   mixed     $value
     * @return  self
     */
    public function set($key, $value) 
    {
        $depth  = explode('/', strtolower($key));
        $config = $this->positions[self::CONFIG_ROOT_KEY];
        $count  = count($depth);

        if ($count == 1) {
            $config->offsetSet($key, $value);
        }

        foreach ($depth as $key => $depthkey) {
            if (!isset($config[$depthkey])) {
                $config->offsetSet($depthkey, new Config([], true));
            }

            $config = $config[$depthkey];

            if ($key == $count - 2) {
                $config->offsetSet($depth[$count - 1], $value);
                break;
            }
        }

        return $this;
    }
}