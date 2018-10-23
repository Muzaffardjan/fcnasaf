<?php
/**
 * Admin module
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Admin;

use Admin\Form\HomeSoccerCupForm;
use Admin\Form\PlayerCardsConfigForm;
use Admin\Form\StaffGroupConfigForm;
use Admin\View\Helper\TemplateBlock;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Module's controllers
use Admin\Controller\IndexController;
use Admin\Controller\SettingsController;

// Forms 
use Admin\Form\LoginForm;
use Admin\Form\MenuConfigForm;

// Listeners
use Admin\Listener\AdminMenuAccessListener;

// View helpers
use Admin\View\Helper\AdminBreadcrumbsHelper;

// Models
use Admin\WebsiteConfig\WebsiteConfig;
use Admin\WebsiteConfig\TemplatePositionsConfig;

/**
 * @todo Controller plugin for dynamic layout
 * example (in controller): 
 * <code>
 *      $this->layoutConfig('tagBody', 'class', ['class1', 'class2']);
 * </code>
 */
class Module implements AutoloaderProviderInterface, ViewHelperProviderInterface
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

    /**
     * {@inheritdoc}
     */
    public function getServiceConfig()
    {
        return [
            'invokables' => [
                // listeners
                AdminMenuAccessListener::class => AdminMenuAccessListener::class,
                WebsiteConfig::class => WebsiteConfig::class,
                PlayerCardsConfigForm::class => PlayerCardsConfigForm::class,
            ], 
            'factories'  => [
                TemplatePositionsConfig::class => function ($services) {
                    return new TemplatePositionsConfig(
                        $services->get(WebsiteConfig::class)
                    );
                },
                MenuConfigForm::class => function ($services) {
                    $instance = new MenuConfigForm();

                    $instance->setTemplatePositionsConfig(
                        $services->get(TemplatePositionsConfig::class)
                    )
                    ->setLocales(
                        $services->get('config')['translator']['locales']
                    );

                    return $instance;
                },
                StaffGroupConfigForm::class => function ($services) {
                    $instance = new StaffGroupConfigForm();

                    $instance->setLocales(
                        $services->get('config')['translator']['locales']
                    );

                    return $instance;
                },
                HomeSoccerCupForm::class => function ($services) {
                    $instance = new HomeSoccerCupForm(
                        null,
                        [
                            'locale' => $services->get('translator')->getLocale()
                        ]
                    );

                    return $instance;
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerConfig()
    {
        return [
            'invokables' => [
                
            ],
            'factories' => [
                'Admin\Controller\Index' => function ($manager) {
                    $services = $manager->getServiceLocator();

                    return new IndexController(
                        new LoginForm(),
                        $services->get(
                            'Zend\Authentication\AuthenticationService'
                        )
                    );
                },
                'Admin\Controller\Settings' => function ($manager) {
                    $services = $manager->getServiceLocator();

                    return new SettingsController(
                        $services->get(WebsiteConfig::class),
                        $services->get(TemplatePositionsConfig::class),
                        $services->get(MenuConfigForm::class),
                        $services->get(StaffGroupConfigForm::class),
                        $services->get(PlayerCardsConfigForm::class),
                        $services->get(HomeSoccerCupForm::class)
                    );
                }   
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'templateBlock' => TemplateBlock::class,
            ],
            'factories' => [
                TemplateBlock::class => function ($vhm) {
                    $instance = new TemplateBlock(
                        $vhm->getServiceLocator()->get('config'),
                        $vhm->getServiceLocator()->get(TemplatePositionsConfig::class)
                    );

                    $instance->setObjectManager(
                        $vhm->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    );

                    return $instance;
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
        $serviceManager      = $e->getApplication()->getServiceManager(); 
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->getSharedManager()->attach(
            'Zend\View\Helper\Navigation\AbstractHelper',
            'isAllowed',
            [
                $serviceManager->get('Admin\Listener\AdminMenuAccessListener'), 
                'isAllowed'
            ]
        );
    }
}
