<?php
namespace Articles;

use ImagesManager\Thumbnail\Thumbnailer;

return [
    'router' => [
        'routes' => [
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'child_routes' => [
                            'articles' => [
                                'type'      => 'Literal',
                                'options'   => [
                                    'route'     => '/articles/manage',
                                    'defaults'  => [
                                        '__NAMESPACE__' => 'Articles\Controller\Manage',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'categories' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/categories[/:action[/:id][.:targetLocale]]',
                                            'constraints' => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                                'id'     => '[a-zA-Z0-9_-]+',
                                                'targetLocale' => '[a-zA-Z-]+',
                                            ],
                                            'defaults'  => [
                                                'controller' => 'Categories',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'articles'   => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'       => '/articles[/:action[/:id][.:targetLocale]]',
                                            'constraints' => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                                'id'     => '[a-zA-Z0-9_-]+',
                                                'targetLocale' => '[a-zA-Z-]+',
                                            ],
                                            'defaults'  => [
                                                'controller' => 'Articles',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'article' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route' => '/pages/:uri[.html]',
                            'constraints' => [
                                'uri'   => '[a-zA-Z0-9_-]+',
                                //'type'  => '[a-z]+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Articles\Controller',
                                'controller'    => 'Articles',
                                'action'        => 'article',
                                //'type'          => 'html',
                            ],
                        ],
                    ],
                    'articles' => [
                        'type'    => 'Segment',
                        'options' =>  [
                            'route'     => '/pages/_list[_:page][.:type]',
                            'constraints' => [
                                'page'  => '[0-9]+',
                                'type'  => '[a-z\.]+',
                            ],
                            'defaults'  => [
                                '__NAMESPACE__' => 'Articles\Controller',
                                'controller'    => 'Articles',
                                'action'        => 'articlesList',
                            ], 
                        ],
                    ],
                    'category'  => [
                        'type'    => 'Segment',
                        'options' => [
                            'route' => '/categories/:uri[.:type]',
                            'constraints' => [
                                'uri'   => '[a-zA-Z0-9_-]+',
                                'page'  => '[0-9]+',
                                'type'  => '[a-z\.]+',
                            ],
                            'defaults'  => [
                                '__NAMESPACE__' => 'Articles\Controller',
                                'controller'    => 'Articles',
                                'action'        => 'byCategory',
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
                'app/admin/articles*' => ['content.manage'],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Articles' => __DIR__ . '/../view',
        ],
    ],
    'navigation' => [
        'admin' => [
            [
                'label'         => 'Categories',
                'route'         => 'app/admin/articles/categories',
                'icon'          => 'wb-grid-4',
                'permission'    => 'content.manage',
                'order'         => 2,
            ],
            [
                'label'         => 'Articles',
                'route'         => 'app/admin/articles/articles',
                'icon'          => 'wb-pencil',
                'permission'    => 'content.manage',
                'order'         => 3, 
            ],
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
    'images-manager' => [
        'thumbnails' => [
            'article_title' => [
                'directory' => 'thumbnails/articles', 
                'width'     => 1024,
                'height'    => 726,
                'quality'   => 80,
                'auto_mode' => false,
                'watermark' => true,
            ],
            'article_body' => [
                'directory' => 'thumbnails/content', 
                'quality'   => 60,
                'watermark' => true,
                'width'     => Thumbnailer::DIMENSION_AUTO,
                'height'    => Thumbnailer::DIMENSION_AUTO,
            ],
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
];
