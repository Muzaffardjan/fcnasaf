<?php
namespace Media;

use ImagesManager\Thumbnail\Thumbnailer;

return [
    'router' => [
        'routes' => [
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'child_routes' => [
                            'media' => [
                                'type'      => 'Literal',
                                'options'   => [
                                    'route'    => '/media/manage',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'Media\Controller\Manage',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes'  => [
                                    'gallery' => [
                                        'type'    => 'Segment',
                                        'options' => [
                                            'route'         => '/gallery[/:action[/:id]]',
                                            'constraints'   => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9_]+', 
                                                'id'     => '[a-zA-Z0-9_]+', 
                                            ],
                                            'defaults'      => [
                                                'controller' => 'PhotoGallery',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'gallery-menu-api' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route'         => '/menu/gallery[/:action[/:id]]',
                                            'constraints'   => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9_]+', 
                                                'id'     => '[a-zA-Z0-9_]+', 
                                            ],
                                            'defaults'      => [
                                                'controller' => 'PhotoGalleryMenuApi',
                                            ],
                                        ],
                                    ],
                                    'video'   => [
                                        'type'        => 'Segment',
                                        'options'     => [
                                            'route'       => '/videos[/:action[/:id]]',
                                            'constraints' => [
                                                'action'=> '[a-zA-Z][a-zA-Z0-9_]+', 
                                                'id'    => '[a-zA-Z0-9_]+', 
                                            ],
                                            'defaults' => [
                                                'controller'=> 'Videos',
                                                'action'    => 'index',
                                            ],
                                        ],
                                    ],
                                    'video-menu-api'   => [
                                        'type'        => 'Segment',
                                        'options'     => [
                                            'route'       => '/menu/videos[/:action[/:id]]',
                                            'constraints' => [
                                                'action'=> '[a-zA-Z][a-zA-Z0-9_]+', 
                                                'id'    => '[a-zA-Z0-9_]+', 
                                            ],
                                            'defaults' => [
                                                'controller'=> 'VideosMenuApi',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'media' => [
                        'type'  => 'Literal',
                        'options' => [
                            'route' => '/media',
                            'defaults' => [
                                '__NAMESPACE__' => 'Media\Controller',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'galleries' => [
                                'type'      => 'Literal',
                                'options'   => [
                                    'route'       => '/galleries.html',
                                    'defaults' => [
                                        'controller' => 'Galleries',
                                        'action'     => 'galleries',
                                    ],
                                ],
                            ],
                            'gallery' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'       => '/gallery/:uri[.html]',
                                    'constraints' => [
                                        'uri' => '[a-zA-Z0-9_]+', 
                                    ],
                                    'defaults' => [
                                        'controller' => 'Galleries',
                                        'action'     => 'view',
                                    ],
                                ],
                            ],
                            'gallery-like' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'       => '/gallery/like/:id',
                                    'constraints' => [
                                        'id' => '[0-9]+', 
                                    ],
                                    'defaults' => [
                                        'controller' => 'Galleries',
                                        'action'     => 'like',
                                    ],
                                ],
                            ],
                            'videos' => [
                                'type'      => 'Literal',
                                'options'   => [
                                    'route'       => '/videos.html',
                                    'defaults' => [
                                        'controller' => 'Videos',
                                        'action'     => 'videos',
                                    ],
                                ],
                            ],
                            'video' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'       => '/video/:uri[.html]',
                                    'constraints' => [
                                        'uri' => '[a-zA-Z0-9_]+', 
                                    ],
                                    'defaults' => [
                                        'controller' => 'Videos',
                                        'action'     => 'view',
                                    ],
                                ],
                            ],
                            'video-like' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'       => '/video/like/:id',
                                    'constraints' => [
                                        'id' => '[0-9]+', 
                                    ],
                                    'defaults' => [
                                        'controller' => 'Videos',
                                        'action'     => 'like',
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
                'app/admin/media*' => ['media.manage'],
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
    'navigation'   => [
        'admin' => [
            [
                'label'         => 'Photo galleries',
                'route'         => 'app/admin/media/gallery',
                'icon'          => 'wb-gallery',
                'permission'    => 'media.manage',
                'order'         => 4,
            ],
            [
                'label'         => 'Videos',
                'route'         => 'app/admin/media/video',
                'icon'          => 'wb-video',
                'permission'    => 'media.manage',
                'order'         => 5,
            ],
        ], 
    ],
    'images-manager' => [
        'thumbnails' => [
            'gallery_front' => [
                'directory' => 'thumbnails/gallery/front', 
                'width'     => 1024,
                'height'    => 726,
                'quality'   => 80,
                'auto_mode' => false,
                'watermark' => true,
            ],
            'video_poster' => [
                'directory' => 'thumbnails/video_posters', 
                'width'     => 1024,
                'height'    => 726,
                'quality'   => 80,
                'auto_mode' => false,
                'watermark' => false,
            ],
            'gallery_small' => [
                'directory' => 'thumbnails/gallery/small', 
                'width'     => 256,
                'height'    => 256,
                'quality'   => 60,
                'auto_mode' => true,
                'watermark' => false,
            ],
            'gallery_photo' => [
                'directory' => 'thumbnails/gallery', 
                'quality'   => 100,
                'watermark' => true,
                'width'     => Thumbnailer::DIMENSION_AUTO,
                'height'    => Thumbnailer::DIMENSION_AUTO,
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Media' => __DIR__ . '/../view',
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
