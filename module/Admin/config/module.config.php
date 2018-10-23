<?php
/**
 * Admin module config
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Admin;

return [
    'router' => [
        'routes' => [
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/admin',
                            'defaults' => [
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'website-config' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route'       => '/websiteConfig[/:action]',
                                    'constraints' => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_]+',
                                    ],
                                    'defaults' => [
                                        'controller' => 'Settings',
                                        'action'     => null
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'login' => [
                'type'      => 'Segment',
                'options'   => [
                    'route'         => '/login[/:locale]',
                    'constraints'   => [
                        'locale' => '[a-zA-Z-]+',
                    ],
                    'defaults'  => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'      => 'Segment',
                'options'   => [
                    'route'         => '/logout[/:locale]',
                    'constraints'   => [
                        'locale' => '[a-zA-Z-]+',
                    ],
                    'defaults'  => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'logout',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Admin' => __DIR__ . '/../view',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'admin' => [
        'view' => [
            'layout' => [
                'primary' => 'admin/layout/layout',
            ],
        ],
    ],
    'navigation' => [
        'adminapps' => [
            [
                'label' => 'Admin main',
                'route' => 'app/admin',
                'icon'  => 'icon wb-dashboard',
                'order' => 1,
            ],
            [
                'label' => 'Website home',
                'route' => 'app/home',
                'icon'  => 'icon wb-home',
                'order' => 2,
            ],
        ],
        'admin' => [
            [
                'label'         => 'Content',
                'uri'           => '#',
                'isCategory'    => true,
                'permission'    => 'manage_content',
                'order'         => 1,
            ],
            [
                'label'         => 'Website config',
                'uri'           => '#',
                'isCategory'    => true,
                'permission'    => 'configuration',
                'order'         => 10,
            ],
            [
                'label'         => 'Menu',
                'route'         => 'app/admin/website-config',
                'action'        => 'menu',
                'icon'          => 'wb-menu',
                'permission'    => 'configuration',
                'order'         => 12,
            ],
            [
                'label'         => 'Staff cards',
                'route'         => 'app/admin/website-config',
                'icon'          => 'wb-fullscreen',
                'permission'    => 'configuration',
                'action'        => 'staffGroup',
                'order'         => 13,
            ],
            [
                'label'         => 'Player cards',
                'route'         => 'app/admin/website-config',
                'icon'          => 'wb-fullscreen',
                'permission'    => 'configuration',
                'action'        => 'playerCards',
                'order'         => 14,
            ],
            [
                'label'         => 'Home page soccer cup',
                'route'         => 'app/admin/website-config',
                'icon'          => 'fa-trophy',
                'permission'    => 'configuration',
                'action'        => 'soccerCup',
                'order'         => 15,
            ],
            [
                'label'         => 'Home page championship background',
                'route'         => 'app/admin/website-config',
                'icon'          => 'fa-cog',
                'permission'    => 'configuration',
                'action'        => 'homeChampionshipBackgroundConfig',
                'order'         => 16,
            ],
            [
                'label'         => 'Championships table config',
                'route'         => 'app/admin/website-config',
                'icon'          => 'fa-table',
                'permission'    => 'configuration',
                'action'        => 'championshipTables',
                'order'         => 17,
            ],
            [
                'label'         => 'Telegram config',
                'route'         => 'app/admin/website-config',
                'icon'          => 'fa-paper-plane',
                'permission'    => 'configuration',
                'action'        => 'telegram',
                'order'         => 18,
            ],
        ],
    ],
    /*'doctrine' => [
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
    ],*/
];
