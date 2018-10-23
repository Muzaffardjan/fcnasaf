<?php 
/**
 * User profile controller
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Users\Form\ProfileSettingsForm;

class ProfileController extends AbstractObjectManagerAwareController
{
    public function __construct()
    {

    }

    public function indexAction()
    {
        $form     = new ProfileSettingsForm();
        $request  = $this->getRequest();
 
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $formData   = $form->getData();
                $user       = $this->identity();  

                // Change password
                if ($formData['password']) {
                    $user->setPassword($formData['password']);
                }

                $user->name = $formData['name'];

                // Save changes
                $this->getObjectManager()->flush();

                // Inform user
                $this->flashmessenger()->addInfoMessage(
                    $this->translate('Your profile settings successfully changed!')
                );

                // return to current page
                return $this->redirect()->toRoute(null, ['locale' => $this->locale()->current()]);
            }
        } else {
            $form->setData($this->identity()->toArray());
            $form->get('password')->setValue('');
        }

        return [
            'form' => $form,
        ];  
    }
}