<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Menu;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Controllers
use Menu\Controller\ContainerController;
use Menu\Controller\PageController;
use Menu\Controller\MenuUrlApiController;
// Forms 
use Menu\Form\MenuUrlForm;
use Menu\Form\MenuEmptyForm;
use Menu\Form\PagesForm;
// Listeners
use Menu\Listener\MenuUrlActionListener;
// Events
use Menu\Event\MenuEvent;

class Module implements AutoloaderProviderInterface
{
    public function getServiceConfig()
    {
        return [
            'factories' => [
                MenuUrlForm::class => function ($sm) {
                    return new MenuUrlForm(
                        null,
                        [
                            'application_config' => $sm->get('config'),
                        ]
                    );
                },
                MenuEmptyForm::class => function ($sm) {
                    return new MenuEmptyForm(
                        null,
                        [
                            'application_config' => $sm->get('config'),
                        ]
                    );
                },
                PagesForm::class => function ($sm) {
                    return new PagesForm(
                        null, 
                        [
                            'application_config' => $sm->get('config'),
                        ]
                    );
                },
                // listener
                MenuUrlActionListener::class => function () {
                    return new MenuUrlActionListener();
                },
            ],
        ]; 
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'Menu\Controller\Container' => function ($cm) {
                    return new ContainerController();
                },
                'Menu\Controller\Page' => function ($cm) {
                    return new PageController(
                        $cm->getServiceLocator()->get(PagesForm::class)
                    );
                },
                'Menu\Controller\MenuUrlApi' => function ($cm) {
                    return new MenuUrlApiController(
                        $cm->getServiceLocator()->get(MenuUrlForm::class),
                        $cm->getServiceLocator()->get(MenuEmptyForm::class)
                    );
                },
            ],
        ]; 
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            $e->getApplication()->getServiceManager()
            ->get(MenuUrlActionListener::class)
        );
    }
}
