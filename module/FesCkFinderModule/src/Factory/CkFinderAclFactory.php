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
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use FesCkFinderModule\User\RoleContext;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CkFinderAclFactory
 */
class CkFinderAclFactory implements FactoryInterface
{
    /**
     * @var CKFinder
     */
    protected $ckFinder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->ckFinder = $serviceLocator->get(CKFinder::class);
        $this->config   = $this->ckFinder['config'];

        $roleContext = new RoleContext();

        $acl = new Acl($roleContext);
        $acl->setRules($this->config->get('accessControl'));

        return $acl;
    }
}