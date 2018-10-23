<?php
namespace ImagesManager;

return array(
    'router' => array(
        'routes' => array(
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'child_routes' => [
                            'images-manager' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'         => '/images[/:action]',
                                    'constraints'   => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                    ], 
                                    'defaults'      => [
                                        '__NAMESPACE__' => __NAMESPACE__.'\Controller',
                                        'controller'    => 'Manager',
                                        'action'        => 'index',
                                    ],
                                ], 
                            ],
                            'images-manager-config' => [
                                'type'      => 'Segment',
                                'options'   => [
                                    'route'         => '/images/settings[/:action]',
                                    'constraints'   => [
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                    ], 
                                    'defaults'      => [
                                        '__NAMESPACE__' => __NAMESPACE__.'\Controller',
                                        'controller'    => 'Configuration',
                                        'action'        => 'index',
                                    ],
                                ], 
                            ],
                        ],
                    ],
                ],
            ],
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ImagesManager' => __DIR__ . '/../view',
        ),
    ),
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
    'zfc_rbac' => [
        'guards' => [
            'ZfcRbac\Guard\RoutePermissionsGuard' => [
                'app/admin/images-manager'        => ['content.manage'],
                'app/admin/images-manager-config' => ['edit.config'],
            ],
        ],
    ],
    'navigation' => [
        'adminapps' => [
            [
                'label' => 'Images manager',
                'route' => 'app/admin/images-manager',
                'icon'  => 'icon wb-image',
                'order' => 3,
            ],
        ],
        'admin'     => [
            [
                'label'         => 'Image display settings',
                'route'         => 'app/admin/images-manager-config',
                'icon'          => 'wb-image',
                'permission'    => 'configuration',
                'order'         => 11,
            ],
        ],
    ],
    'images-manager' => [
        'images_directory'      => 'public/images',
        'temp_images_directory' => 'public/images/temp',
        'thumbnails'            => [
            'list' => [
                // root will be above images_directory config
                'directory' => 'thumbnails/list', 
                'width'     => 256,
                'height'    => 256,
                'quality'   => 60,
                'auto_mode' => true,
                // Use watermark for this thumbnail?
                // priority of this option is highest
                // this means that whatever is watermark using enabled by user
                // if it set to false 
                // watermark will not be used by this thumbnail
                'watermark' => false,
            ],
        ],
        'config'    => [
            'filepath'        => __DIR__ . '/user.config.php',
            'watermark_path'  => __DIR__ . '/../files/watermark.png', 
        ],
        'lister'    => [
            'folders'   => [
                'denied' => [
                    'public/images/thumbnails',
                    'public/images/temp',
                ],
            ],
            'images'    => [
                'validators' => [
                    [
                        'name' => 'File\IsImage',
                    ],
                ], 
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
);
