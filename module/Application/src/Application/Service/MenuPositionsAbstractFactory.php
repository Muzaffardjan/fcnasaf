<?php
/**
 * Template menu positions abstract factory
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\Navigation\Navigation;
//use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Admin\WebsiteConfig\TemplatePositionsConfig;

/**
 * Navigation abstract service factory
 *
 * Allows configuring several navigation instances. If you have a navigation config key named "special" then you can
 * use $container->get('Zend\Navigation\Special') to retrieve a navigation instance with this configuration.
 */
final class MenuPositionsAbstractFactory implements AbstractFactoryInterface
{
    /**
     * Top-level configuration key indicating navigation configuration
     *
     * @var string
     */
    const ENTITY = 'Menu\Entity\Container';

    const TEMPLATE_POSITION_KEY = 'navigations';

    /**
     * Service manager factory prefix
     *
     * @var string
     */
    const SERVICE_PREFIX = 'Template\\Navigation\\';

    /**
     * Navigation configuration
     *
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $cached;

    /**
     * Can we create a navigation by the requested name? (v3)
     *
     * @param ContainerInterface $container
     * @param string $requestedName Name by which service was requested, must
     *     start with Zend\Navigation\
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (0 !== strpos($requestedName, self::SERVICE_PREFIX)) {
            return false;
        }
        
        $exploded = explode('\\', strtolower($requestedName));
        $nav      = $exploded[count($exploded) - 1];
        $config   = $container->get(TemplatePositionsConfig::class)
        ->get(self::TEMPLATE_POSITION_KEY);

        if (!$config->offsetExists($nav)) {
            return false;
        }

        $config = $config->offsetGet($nav)->offsetGet('menu');
        $locale = $container->get('translator')->getLocale();

        if ($config->offsetGet($locale) || $config->offsetGet('default')) {
            if ($config->offsetGet($locale)) {
                $id = $config->offsetGet($locale);
            } else {
                $id = $config->offsetGet('default');
            }

            $this->cached[$requestedName] = $container
            ->get('Doctrine\ORM\EntityManager')
            ->getRepository(self::ENTITY)
            ->find($id);

            if ($this->cached[$requestedName]) {
                $this->cached[$requestedName] = $this->cached[$requestedName]->toArray();
                return true;
            }
        }

        return false;
    }

    /**
     * Can we create a navigation by the requested name? (v2)
     *
     * @param ServiceLocatorInterface $container
     * @param string $name Normalized name by which service was requested;
     *     ignored.
     * @param string $requestedName Name by which service was requested, must
     *     start with Zend\Navigation\
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        return $this->canCreate($container, $requestedName);
    }

    /**
     * {@inheritDoc}
     *
     * @return Navigation
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $factory = new ConstructedNavigationFactory($this->cached[$requestedName]);
        return $factory($container, $requestedName);
    }

    /**
     * Can we create a navigation by the requested name? (v2)
     *
     * @param ServiceLocatorInterface $container
     * @param string $name Normalized name by which service was requested;
     *     ignored.
     * @param string $requestedName Name by which service was requested, must
     *     start with Zend\Navigation\
     * @return Navigation
     */
    public function createServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        return $this($container, $requestedName);
    }
}
