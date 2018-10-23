<?php
/**
 * This file is placed here for compatibility with Zendframework 2's ModuleManager.
 * It allows usage of this module even without composer.
 */
namespace FesCkFinderModule;

function zf2_compatible_auto_loader_config()
{
    return array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                __NAMESPACE__ => __DIR__ . '/src',
            ),
        ),
    );
}

require_once __DIR__ . '/src/Module.php';