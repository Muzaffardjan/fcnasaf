<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\Factory;

use CKSource\CKFinder\CKFinder;
use FesCkFinderModule\Controller\ConnectorController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ConnectorControllerFactory
 */
class ConnectorControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ConnectorController(
            $serviceLocator->getServiceLocator()->get(CKFinder::class)
        );
    }
}