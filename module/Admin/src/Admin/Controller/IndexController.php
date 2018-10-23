<?php 
/**
 * Admin module
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Admin\Controller;

use Zend\Authentication\AuthenticationService;

use Admin\Form\LoginForm;

class IndexController extends AbstractObjectManagerAwareController
{
    /**
     * @var LoginForm $loginForm
     */
    protected $loginForm;

    /**
     * @var AuthenticationService $authenticationService
     */
    protected $authenticationService;

    public function __construct(
        LoginForm $loginForm, 
        AuthenticationService $authenticationService
    ) {
        $this->loginForm                = $loginForm;
        $this->authenticationService    = $authenticationService;
    }

    public function indexAction()
    {
        return [];
    }

    public function logoutAction()
    {
        // Clear session
        $this->authenticationService->getStorage()->clear();

        return $this->redirect()->toRoute(
            'login', 
            ['locale' => $this->locale()->current()]
        );
    }

    public function loginAction()
    {
        if ($this->identity() && $this->isGranted('admin.access')) {
            return $this->redirect()
                ->toRoute(
                    'app/admin',
                    [],
                    true
                );
        }

        // Set login layout
        $this->layout('admin/layout/login');
        // Get form 
        $form       = $this->loginForm;
        $request    = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                $result = $this->authenticationService
                ->getAdapter()
                ->setIdentity($formData['login'])
                ->setPassword($formData['password'])
                ->authenticate();

                if ($result->isValid()) {
                    $this->authenticationService->getStorage()
                    ->write(
                        $result->getIdentity()
                    );

                    return $this->redirect()->toRoute(
                        'app/admin', 
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
                } else {
                    $form->get('login')
                    ->setMessages(
                        [
                            $this->translate('Username or password is invalid')
                        ]
                    );
                }
            }
        }

        return [
            'form' => $form,
        ];
    }
}