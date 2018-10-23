<?php
/**
 * ImagesManager
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace ImagesManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Controllers
use ImagesManager\Controller\ManagerController;
use ImagesManager\Controller\ConfigurationController;

// Forms 
use ImagesManager\Form\Configuration as ConfigurationForm;

// Models
use ImagesManager\Configuration\Config as ImagesManagerConfig;
use ImagesManager\Watermarker;
use ImagesManager\Thumbnail\Storage\Organizer as ThumbnailStorageOrganizer;
use ImagesManager\Thumbnail\Thumbnailer;
use ImagesManager\Thumbnail\ThumbnailerFactory;

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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() 
    {
        return [
            'invokables' => [
                'ImagesManager\Form\Upload' => 'ImagesManager\Form\Upload',
                'ImagesManager\Form\CreateFolder' => 'ImagesManager\Form\CreateFolder',
            ],
            'factories' => [
                ConfigurationForm::class => function ($services) {
                    return new ConfigurationForm(
                        null, 
                        // injecting config
                        $services->get('config')['images-manager']['config']
                    );
                },
                ImagesManagerConfig::class => function ($services) {
                    return new ImagesManagerConfig(
                        $services->get('config')['images-manager']['config']['filepath'],
                        false
                    );
                },
                (ImagesManagerConfig::class) . '\ReadOnly' => function ($services) {
                    return new ImagesManagerConfig(
                        $services->get('config')['images-manager']['config']['filepath'],
                        true
                    );
                }, 
                Watermarker::class => function ($services) {
                    $options = [];
                    $config  = $services->get('config')['images-manager']['config'];
                    $options = include $config['filepath'];

                    $options['watermark_path'] = $config['watermark_path'];

                    return new Watermarker(
                        $services->get('WebinoImageThumb'),
                        $options
                    );  
                },
                ThumbnailStorageOrganizer::class => function ($services) {
                    return new ThumbnailStorageOrganizer(
                        $services->get('config')['images-manager']
                    );
                },
                Thumbnailer::class => function ($services) {
                    return new Thumbnailer(
                        $services->get('WebinoImageThumb'),
                        [
                            'watermarker'       => $services
                            ->get(Watermarker::class),
                            'storage_organizer' => $services
                            ->get(ThumbnailStorageOrganizer::class),
                        ]
                    );
                },
            ],
        ]; 
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'ImagesManager\Controller\Manager' => function ($cm) {
                    $services = $cm->getServiceLocator();

                    return new ManagerController(
                        $services->get(Thumbnailer::class),
                        $services->get('config')['images-manager'],
                        $services->get('ImagesManager\Form\Upload'),
                        $services->get('ImagesManager\Form\CreateFolder')
                    );
                },
                'ImagesManager\Controller\Configuration' => function ($cm) {
                    $services = $cm->getServiceLocator();

                    return new ConfigurationController(
                        $services->get(ConfigurationForm::class),
                        $services->get(ImagesManagerConfig::class),
                        $services->get('config')['images-manager']
                    );
                }
            ],
        ];
    }

    public function getModuleDependencies()
    {
        /**
         * This module didn't work without WebinoImageThumb
         */
        return [
            'WebinoImageThumb',
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
