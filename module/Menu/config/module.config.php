<?php
namespace Menu;

return [
    'router' => [
        'routes' => [
            'app' =>[
                'child_routes' => [
                    'admin'     => [
                        'child_routes' => [
                            'menu' => [
                                'type'          => 'Literal',
                                'options'       => [
                                    'route'    => '/menu',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Menu\Controller',
                                    ],
                                ],
                                /*'may_terminate' => true,*/
                                'child_routes'  => [
                                    'url-api'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'     => '/api/url[/:container]',
                                            'constraints' => [
                                                'container' => '[0-9]+',
                                            ],
                                            'defaults'  => [
                                                'controller'=> 'MenuUrlApi',
                                                'action'    => 'index',
                                            ],
                                        ], 
                                    ], 
                                    'empty-element-api'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'     => '/api/empty[/:container]',
                                            'constraints' => [
                                                'container' => '[0-9]+',
                                            ],
                                            'defaults'  => [
                                                'controller'=> 'MenuUrlApi',
                                                'action'    => 'container',
                                            ],
                                        ], 
                                    ], 
                                    'container' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/container[/:action[/:id]]',
                                            'constraints' => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                                'id'     => '[0-9]+',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Container',
                                                'action'     => 'index',
                                            ],
                                        ], 
                                    ],
                                    'page' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/page[/:action[/:id]]',
                                            'constraints' => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                                'id'     => '[0-9]+',
                                            ],
                                            'defaults'    => [
                                                'controller' => 'Page',
                                            ],
                                        ], 
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RoutePermissionsGuard' => [
                'app/admin/menu*' => ['configuration'],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Menu' => __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__.'_entities' => [
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/'.__NAMESPACE__.'/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__.'\Entity' => __NAMESPACE__.'_entities',
                ],
            ],
        ],
    ],
    'navigation' => [
        'adminapps' => [
            [
                'label' => 'Navigation editor',
                'route' => 'app/admin/menu/container',
                'icon'  => 'icon wb-menu',
                'order' => 4
            ],
        ], 
    ],
];
