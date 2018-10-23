<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\Factory;

use CKSource\CKFinder\Acl\Acl;
use CKSource\CKFinder\Authentication\CallableAuthentication;
use CKSource\CKFinder\CKFinder;
use FesCkFinderModule\EventDispatcher;
use FesCkFinderModule\Module;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * CKFinderFactory
 */
class CKFinderFactory implements FactoryInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var CKFinder
     */
    protected $ckFinder;

    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;
        $this->ckFinder       = new CKFinder($this->getConfig());

        // prepare CKFinder inject overridden services
        $this->prepare();

        return $this->ckFinder;
    }

    protected function getConfig()
    {
        if (null === $this->config) {
            $config = $this->serviceManager->get('config');

            if (isset($config[Module::CK_FINDER_CONFIG])) {
                $this->config = $config[Module::CK_FINDER_CONFIG];
            } else {
                $this->config = array();
            }
        };

        return $this->config;
    }

    protected function prepare()
    {
        $serviceManager = $this->serviceManager;
        $factory        = $this;

        // Authentication callable inject
        $this->ckFinder['authentication'] = $this->serviceManager->get(CallableAuthentication::class);

        // EventDispatcher
        $this->ckFinder['dispatcher']     = $this->serviceManager->get(EventDispatcher::class);

        // Acl inject
        $this->ckFinder['acl']            = function () use ($serviceManager) {
            return $serviceManager->get(Acl::class);
        };
    }
}