<?php
/**
 * User module
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Config\Config;
use Zend\Console\Adapter\AdapterInterface as Console;

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
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
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
        return array(
            'aliases'       => array(
                'Zend\Authentication\AuthenticationService' => 'Users\Authentication\AuthenticationService',
            ),
            'invokables'    => array(
                //
            ),
            'factories'     => array(
                'Users\Authentication\AuthenticationService' => function($sm) {
                    $entityManager = $sm->get('Doctrine\ORM\EntityManager');
                    $service = new Authentication\AuthenticationService();
                    $adapter = new \Users\Authentication\Adapter\DoctrineORMAdapter(
                        // inject object manager
                        $entityManager,
                        // Config
                        new Config($sm->get('config')['users']['authentication']['adapter']['doctrine_orm'])
                    );
                    $storage = new \Users\Authentication\Storage\Storage(
                        // Inject result entity repository
                        $entityManager->getRepository('Users\Entity\User')
                    );

                    // Set custom adapter
                    $service->setAdapter($adapter);
                    // Set custom storage
                    $service->setStorage($storage);

                    return $service;
                },
            ),
        );
    }

    // Controllers config of a module
    public function getControllerConfig()
    {
        return array(
            'invokables'    => [
                'Users\Controller\Profile' => Controller\ProfileController::class,
            ],
            'factories'     => [
                'Users\Controller\Manage' => function($cm) {
                    $sm = $cm->getServiceLocator();

                    // inject dependencies and return an instance
                    return new Controller\ManageController();
                },
            ],
        );
    }

    public function getConsoleUsage(Console $console)
    {
        return array(
            // Create superuser
            ['create superuser --username --password [--name]', 'Creates new superuser'],
            ['--username', 'User login'],
            ['--password', 'User password'],
            ['--name', '[optional] Name of user, default is Jonh Doe'],
            // Password reset
            ['reset password --username --password','Resets user password'],
            ['--username', 'User login'],
            ['--password', 'New password'],
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $application         = $e->getApplication();
        $eventManager        = $application->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach(
            $application->getServiceManager()->get(
                'ZfcRbac\View\Strategy\RedirectStrategy'
            )
        );
    }
}
