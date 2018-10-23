<?php
/**
 * ConstructedNavigationFactory
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Application\Service;

use Interop\Container\ContainerInterface;
use Rbac\Traversal\RecursiveRoleIterator;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc;
use Zend\Navigation\Service\ConstructedNavigationFactory as ZendConstructedNavigationFactory;

class ConstructedNavigationFactory extends ZendConstructedNavigationFactory
{
    public function getPages(ContainerInterface $container)
    {
        $pages      = parent::getPages($container);
        $routeMatch = $container->get('application')->getMvcEvent()->getRouteMatch();


        if ($routeMatch) {
            $params = $routeMatch->getParams();

            if (isset($params['locale'])) {
                return $this->setLocaleIfNeeded(
                    is_array($pages) ? $pages : $pages->toArray(),
                    $params['locale']
                );
            }
        }

        return $pages;
    }

    /**
     * @param array $navigation
     * @param $locale
     * @return array
     */
    public function setLocaleIfNeeded(array $navigation, $locale)
    {
        foreach ($navigation as $key => $page) {
            if (isset($page['route'])) {
                if (!isset($page['params']['locale']) || !$page['params']['locale']) {
                    $page['params']['locale'] = $locale;
                }
            }

            if (isset($page['pages']) && is_array($page['pages']) && $page['pages']) {
                $page['pages'] = $this->setLocaleIfNeeded($page['pages'], $locale);
            }

            $navigation[$key] = $page;
        }

        return $navigation;
    }
}