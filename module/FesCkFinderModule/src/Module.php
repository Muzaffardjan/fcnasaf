<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */

namespace FesCkFinderModule;

require_once __DIR__ . '/../vendor/autoload.php';use CKSource\CKFinder\CKFinder;
use Zend\Mvc\MvcEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Module
{
    // config key
    const CK_FINDER_CONFIG        = 'ck_finder';

    public function onBootstrap(MvcEvent $event)
    {
        /**
         * @var EventDispatcher $eventDispatcher
         * @var CKFinder        $ckFinder
         */
        $services        = $event->getApplication()->getServiceManager();
        $eventDispatcher = $services->get(EventDispatcher::class);
        $ckFinder        = $services->get(CKFinder::class);

        $eventDispatcher->addListener(KernelEvents::VIEW, array($ckFinder, 'createResponse'), -512);
        $eventDispatcher->addListener(KernelEvents::RESPONSE, array($ckFinder, 'afterCommand'), -512);
        $eventDispatcher->addSubscriber($ckFinder['exception_handler']);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        if (is_callable(__NAMESPACE__ . '\\zf2_compatible_auto_loader_config')) {
            return zf2_compatible_auto_loader_config();
        }

        return [];
    }
}
