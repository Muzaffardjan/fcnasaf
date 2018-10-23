<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
            'Zend\Navigation\Service\NavigationAbstractServiceFactory',          
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            // Add json model support
            'ViewJsonStrategy',
        ),
    ),
    // config of global di
    'di' => array(
        'definition' => array(
            'class' => array(
                'Zend\Log\Logger' => array(
                    'methods' => array(
                        'addWriter' => array(
                            'writer' => array(
                                'required'  => true,
                                'type'      => 'Zend\Log\Writer\Stream', 
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'instance' => array(
            'Zend\Log\Logger' => array(
                'injections' => array(
                    'Zend\Log\Writer\Stream',
                ),
            ),
            'Zend\Log\Writer\Stream' => array(
                'parameters' => array(
                    'streamOrUrl' => __DIR__.'/../../data/logs/log_'.date('d_m_Y').'.txt',
                ),
            ),
        ),
    ),
);
