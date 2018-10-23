<?php 
/**
 * Authentification service
 * little overwrite zf2 service
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @license Foreach.Soft 2016
 * @version 1.0.0
 */
namespace Users\Authentication;

use Zend\Authentication\AuthenticationService as ZendAuthenticationService;
use ZfcRbac\Identity\IdentityProviderInterface;

class AuthenticationService 
      extends ZendAuthenticationService 
      implements IdentityProviderInterface
{

}   