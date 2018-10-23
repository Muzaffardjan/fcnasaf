<?php
/**
 * Users management controller
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\Controller;

use Zend\Console\Request as ConsoleRequest;

use Zend\Mvc\Controller\Plugin\FlashMessenger;

use Admin\Controller\AbstractObjectManagerAwareController;
use Users\Entity\User;
use Users\ZfcRbac\Role\Role;

class ManageController extends AbstractObjectManagerAwareController
{
    public function indexAction()
    {
        $flashmessage = $this->flashmessenger();

        return [];
    }

    public function addAction()
    {
        return [];
    }

    public function editAction()
    {
        return [];
    }

    public function deleteAction()
    {
        return [];
    }

    /**
     * Console only action
     */
    public function createSuperUserAction()
    {
        $request        = $this->getRequest();
        $objectManager  = $this->getObjectManager();

        if ($request instanceof ConsoleRequest) {
            // user defaults
            $user   = [
                'username'  => false,
                'password'  => false,
                'roles'     => [Role::SUPERUSER],
                'name'      => 'Jonh Doe',
            ];

            foreach ($this->params()->fromRoute() as $key => $value) {
                $user[$key] = $value;
            }

            $newUser          = User::factory($user);

            // Set password
            $newUser->setPassword($newUser->password);

            // Insert all changes
            $objectManager->persist($newUser);

            // Save changes
            $objectManager->flush();

            return "Creating superuser. Done.";
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    /**
     * Console only action
     */
    public function consoleResetPasswordAction()
    {
        $request        = $this->getRequest();
        $objectManager  = $this->getObjectManager();

        if ($request instanceof ConsoleRequest) {
            $repository = $this->getObjectManager()->getRepository(
                'Users\Entity\User'
            );
            $parametres = $this->params()->fromRoute();

            if (
                $user = $repository->findOneBy(
                    [
                        'username' => $parametres['username'],
                    ]
                )
            ) {
                $user->setPassword($parametres['password']);
                $objectManager->flush();

                return sprintf(
                    "Changing user password for: %s... Done.",
                    $user->username
                );
            }

            return "User with this name not found!";
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}