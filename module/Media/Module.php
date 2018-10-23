<?php
/**
 * Media module
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */

namespace Media;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Models
use Media\Likes;
// Controllers
use Media\Controller\GalleriesController;
use Media\Controller\VideosController;
// Manage
use Media\Controller\Manage\PhotoGalleryController;
use Media\Controller\Manage\VideosController as ManageVideosController;
use Media\Controller\Manage\PhotoGalleryMenuApiController;
use Media\Controller\Manage\VideosMenuApiController;
// Helpers
use Media\View\Helper\GalleriesHelper;
use Media\View\Helper\VideosHelper;
// Listeners
use Media\Listener\MenuApiListener;
//Forms
use Media\Form\MenuGalleryForm;
use Media\Form\MenuVideoForm;

class Module implements AutoloaderProviderInterface
{
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

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Likes::class => function ($sm) {
                    return new Likes(
                        $sm->get('request'),
                        $sm->get('response')
                    );
                },
                MenuApiListener::class => function ($sm) {
                    return new MenuApiListener();
                },
                MenuGalleryForm::class => function ($sm) {
                    return new MenuGalleryForm(
                        $sm->get('Doctrine\ORM\EntityManager')
                    );
                },
                MenuVideoForm::class => function ($sm) {
                    return new MenuVideoForm(
                        $sm->get('Doctrine\ORM\EntityManager')
                    );
                }
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'Media\Controller\Manage\PhotoGallery' => function () {
                    return new PhotoGalleryController();
                },
                'Media\Controller\Manage\PhotoGalleryMenuApi' => function ($cm) {
                    return new PhotoGalleryMenuApiController(
                        $cm->getServiceLocator()->get('Menu\Form\PagesForm'),
                        $cm->getServiceLocator()->get(MenuGalleryForm::class)
                    );
                },
                'Media\Controller\Manage\Videos' => function () {
                    return new ManageVideosController();
                },
                'Media\Controller\Manage\VideosMenuApi' => function ($cm) {
                    return new VideosMenuApiController(
                        $cm->getServiceLocator()->get('Menu\Form\PagesForm'),
                        $cm->getServiceLocator()->get(MenuVideoForm::class)
                    );
                },
                'Media\Controller\Galleries' => function ($cm) {
                    return new GalleriesController(
                        $cm->getServiceLocator()->get(Likes::class)
                    );
                },
                'Media\Controller\Videos' => function ($cm) {
                    return new VideosController(
                        $cm->getServiceLocator()->get(Likes::class)
                    );
                },
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'galleries' => function ($hpm) {
                    return new GalleriesHelper(
                        $hpm->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    );
                },
                'videos' => function ($hpm) {
                    return new VideosHelper(
                        $hpm->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    );
                }
            ],
        ];  
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
            ->get(MenuApiListener::class)
        );
    }
}
