<?php
namespace FesCkFinderModule;

return array(
    'controllers' => array(
        'factories' => array(
            Controller\ConnectorController::class => Factory\ConnectorControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'fes-ck-finder-module' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/ckfinder/core/connector/php',
                    'defaults' => array(
                        'controller' => Controller\ConnectorController::class,
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            \CKSource\CKFinder\Authentication\CallableAuthentication::class => Factory\CallableAuthenticationFactory::class,
            \CKSource\CKFinder\CKFinder::class => Factory\CKFinderFactory::class,
            \CKSource\CKFinder\Acl\Acl::class  => Factory\CkFinderAclFactory::class,
            EventDispatcher::class             => Factory\EventDispatcherFactory::class,
        ),
    ),
);
