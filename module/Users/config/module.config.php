<?php
namespace Users;

use Users\Entity\Role;

return [
    'router' => [
        'routes' => [
            'app' => [
                'child_routes' => [
                    'admin' => [
                        'child_routes' => [
                            'user' => [
                                'type'      => 'Literal',
                                'options'   => [
                                    'route'     => '/users',
                                    'defaults'  => [
                                        '__NAMESPACE__' => 'Users\Controller',
                                    ],
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'profile' => [
                                        'type'      => 'Literal',
                                        'options'   => [
                                            'route'     => '/profile',
                                            'defaults'  => [
                                                'controller' => 'Profile',
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'manage' => [
                                        'type'      => 'Segment',
                                        'options'   => [
                                            'route'         => '/manage[/:action]',
                                            'constraints'   => [
                                                'action' => '[a-zA-Z][a-zA-Z0-9]+',
                                            ],
                                            'defaults'      => [
                                                'controller' => 'Manage',
                                                'action'     => 'index',
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
                'app/admin/user/profile' => ['admin.main'],
                'app/admin/user/manage'  => ['users.manage'],
            ],
        ],
    ],
    'view_manager' => [
        /*'template_map'          => [
            
        ],*/
        'template_path_stack'   => [
            __DIR__ . '/../view',
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
    'users' => [
        'authentication' => [
            'adapter' => [
                'doctrine_orm' => [
                    'entity' => 'Users\Entity\User',
                ],
            ],
        ],
    ],
    // module permissions
    /**
     * <code>
     *      [
     *          'permission_name' => 'Permission description',
     *      ]
     * </code>
     */
    'permissions' => [
        
    ],
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ),
        ),
    ),
    'console' => [
        'router' => [
            'routes' => [
                'create_superuser' => [
                    'options' => [
                        'route'     => 'create superuser --username= --password= [--name=]',
                        'defaults'  => [
                            '__NAMESPACE__' => 'Users\Controller',
                            'controller'    => 'Manage',
                            'action'        => 'createSuperUser'
                        ], 
                    ],
                ],
                'reset_password' => [
                    'options' => [
                        'route'     => 'user reset password --username= --password=',
                        'defaults'  => [
                            '__NAMESPACE__' => 'Users\Controller',
                            'controller'    => 'Manage',
                            'action'        => 'consoleResetPassword',
                        ],
                    ],
                ],
            ],
        ],
    ],
];