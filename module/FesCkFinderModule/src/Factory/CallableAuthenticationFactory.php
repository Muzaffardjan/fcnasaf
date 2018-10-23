<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\Factory;

use CKSource\CKFinder\Authentication\CallableAuthentication;
use FesCkFinderModule\Module;
use FesCkFinderModule\Exception;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * CallableAuthenticationFactory
 */
class CallableAuthenticationFactory implements FactoryInterface
{
    const DEFAULT_AUTHENTICATION_SERVICE = 'Zend\Authentication\AuthenticationService';

    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        if (isset($config[Module::CK_FINDER_CONFIG]['authenticationService'])) {
            $authService = $config[Module::CK_FINDER_CONFIG]['authenticationService'];
        } else {
            $authService = self::DEFAULT_AUTHENTICATION_SERVICE;
        }

        $authService = $serviceLocator->get($authService);

        if (!$authService instanceof AuthenticationServiceInterface) {
            throw new Exception\InvalidConfigException(
                'Authentication service must implement Zend\Authentication\AuthenticationServiceInterface'
            );
        }

        return new CallableAuthentication(
            function () use ($authService) {
                /**
                 * @var AuthenticationServiceInterface $authService
                 */
                return $authService->hasIdentity();
            }
        );
    }
}