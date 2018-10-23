<?php
/**
 * Application Module
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Application;

use Application\Filter\Entity as EntityFilter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Router\RouteMatch;

use Application\Listener\ObjectManagerAwareControllerListener;
use Application\Listener\LocaleChekerListener;
use Application\Controller\AbstractObjectManagerAwareController;
use Application\View\Helper\Locale as LocaleViewHelper;
use Application\Controller\Plugin\ApiCall as ApiCallControllerPlugin;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application         = $e->getApplication();
        $eventManager        = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            $application->getServiceManager()->get(
                'Application\Listener\LocaleChekerListener'
            )
        );

        $eventManager->attach(
            $application->getServiceManager()->get(
                'Application\Listener\ObjectManagerAwareControllerListener'
            )
        );

        $eventManager->attach(
            $application->getServiceManager()->get(Listener\TelegramListener::class)
        );
        
        /**
         * Zf2 View Url helper bug fix
         * The problem is Url helper always requires RouteMatch
         * if you used null in route name or set reuse matches to true
         * even in 404 error but 404 itself means that there is not any route match ;)
         */
        $eventManager->attach(
        /**
         * @param MvcEvent $event
         */
            MvcEvent::EVENT_DISPATCH_ERROR,
            function ($event) {
                $application    = $event->getApplication();
                $serviceLocator = $application->getServiceManager();
                // default locale, app need locale in any route
                $locale         = $serviceLocator->get(
                    'config'
                )['translator']['locale'];
                $match          = $application->getMvcEvent()
                ->getRouteMatch();

                if ($event->getViewModel()) {
                    $event->getViewModel()->setTemplate(
                        $serviceLocator->get('config')['view_manager']['error_layout']
                    );
                }
                
                if (null === $match) {
                    $params     = [
                        'locale'        => $locale,
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'not-found',
                    ];
                    $routeMatch = new RouteMatch($params);

                    $routeMatch->setMatchedRouteName('home');
                    $application->getMvcEvent()->setRouteMatch(
                        $routeMatch
                    );
                }
            }
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function (
                    $instance, 
                    ServiceLocatorInterface $controllerManager
                ) {
                    $services = $controllerManager->getServiceLocator();

                    if ($instance instanceof LoggerAwareInterface) {
                        $instance->setLogger(
                            $services->get('di')
                            ->get('Zend\Log\Logger')
                        );
                    }

                    if ($instance instanceof ObjectManagerAwareInterface) {
                        $instance->setObjectManager(
                            $services->get('Doctrine\ORM\EntityManager')
                        );
                    }
                }
            ),
            'factories' => array(
                'Application\Controller\Index' => function ($cm) {
                    $sm = $cm->getServiceLocator();
                    return new Controller\IndexController(
                        $sm->get('Doctrine\ORM\EntityManager'),
                        $sm->get(\Admin\WebsiteConfig\TemplatePositionsConfig::class)->get()
                    );
                },
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'locale' => function ($pluginManager) {
                    return new Controller\Plugin\Locale(
                        $pluginManager->getServiceLocator()->get('translator'),
                        $pluginManager->getServiceLocator()->get('config')['translator']
                    );
                },
                'translate' => function ($pluginManager) {
                    return new Controller\Plugin\Translate(
                        $pluginManager->getServiceLocator()->get('translator')
                    );
                },
                'apiCall' => function ($services) {
                    return new ApiCallControllerPlugin(
                        $services->getServiceLocator()->get('request')
                    );
                }
            ],
        ];  
    }

    public function getServiceConfig()
    {
        return array(
            'invokables'    => array(
                'Application\Listener\ObjectManagerAwareControllerListener' 
                    => ObjectManagerAwareControllerListener::class,
                'Application\Listener\LocaleChekerListener' 
                    => LocaleChekerListener::class
            ),
            'factories'     => array(
                Social\Telegram\Bot::class => function ($sm) {
                    return new Social\Telegram\Bot(
                        '321549800:AAGfy8CBMZZj3S4qqnJfnI85pKC6q3Spef8',
                        [44632611, '@FcNasafOfficial']
                    );
                },
                Listener\TelegramListener::class => function ($sm) {
                    return new Listener\TelegramListener($sm->get(Social\Telegram\Bot::class));
                }
            ),
            'initializers'  => array(
                function ($instance, ServiceLocatorInterface $services) {
                    if ($instance instanceof LoggerAwareInterface) {
                        $instance->setLogger(
                            $services->get('di')
                            ->get('Zend\Log\Logger')
                        );
                    }
                    
                    if ($instance instanceof ObjectManagerAwareInterface) {
                        $instance->setObjectManager(
                            $services->get('Doctrine\ORM\EntityManager')
                        );
                    }
                }
            ),
            'abstract_factories' => [
                'Application\Service\MenuPositionsAbstractFactory',
            ],
        );
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'locale' => function ($hpm) {
                    return new LocaleViewHelper(
                        $hpm->getServiceLocator()->get('translator'),
                        $hpm->getServiceLocator()->get('config')
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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
