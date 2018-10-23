<?php
/**
 * Application config
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Application;

return [
    'router' => [
        'routes' => [
            'app'   => [
                'type'      => 'Regex',
                'options'   => [
                    'regex' => '/(?<locale>[a-zA-Z-]+)',
                    'spec'  => '/%locale%',
                    'defaults' => [
                        'locale' => 'uz-latn-uz',
                    ],
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    // Child routes
                    'home' => [
                        'type'    => 'Segment',
                        'options' => [
                            /**
                             * @todo rethink this config
                             */
                            'route'     => '[/:action]',
                            'defaults'  => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ],
                        ], 
                    ],
                ],
            ],
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'     => '/',
                    'defaults'  => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'set-locale',
                    ],
                ],
            ],
            'fes-ck-finder' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/assets/ckfinder/core/connector/php/connector.php',
                    'defaults' => array(
                        'controller' => \FesCkFinderModule\Controller\ConnectorController::class,
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(),
            ),
        ],
    ],
    'translator' => [
        // default fallback locale
        'locale' => 'uz-latn-uz',
        // Locales that allowed and exists
        'locales'       => [
            'en-us'      => 'English',
            'ru-ru'      => 'Русский',
            'uz-cyrl-uz' => 'Ўзбекча',
            'uz-latn-uz' => 'O\'zbekcha',
        ],
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'error_layout'             => 'layout/layout',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
            ],
        ],
    ],
];