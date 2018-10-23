<?php 
/**
 * MenuUrlApiController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Menu\Form\MenuUrlForm;
use Menu\Form\MenuEmptyForm;
use Menu\Entity\Page;

class MenuUrlApiController extends AbstractObjectManagerAwareController
{   
    /**
     * @var MenuUrlForm
     */
    protected $formUrl;

    /**
     * @var MenuEmptyForm
     */
    protected $formEmpty;

    /**
     * Init
     */
    public function __construct(MenuUrlForm $formUrl, MenuEmptyForm $formEmpty)
    {
        $this->setUrlForm($formUrl)
        ->setEmptyElementForm($formEmpty);
    }

    public function indexAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if (!($this->params('container') && $container = $repository->find($this->params('container')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $form    = $this->getUrlForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $page = new Page();

                $page->setLabel($data['label'])
                ->setLocale($data['locale'] ?: null)
                ->setTarget($data['target'])
                ->setTitle($data['title'])
                ->setUri($data['url'])
                ->setInfo('URL')
                ->setActive(true)
                ->setVisible(true);

                $this->getObjectManager()->persist($page);
                $container->getPages()->add($page);

                return $this->redirect()->toRoute(
                    'app/admin/menu/container',
                    [
                        'locale' => $this->locale()->current(),
                        'action' => 'edit',
                        'id'     => $container->getId(),
                    ]
                );  
            }
        }

        return [
            'form'      => $form,
            'container' => $container,
        ];
    }

    public function containerAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if (!($this->params('container') && $container = $repository->find($this->params('container')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $form    = $this->getEmptyElementForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $page = new Page();

                $page->setLabel($data['label'])
                ->setLocale($data['locale'] ?: null)
                ->setTitle($data['title'])
                ->setUri('javascript:void(0);')
                ->setInfo('Container')
                ->setActive(true)
                ->setVisible(true);

                $this->getObjectManager()->persist($page);
                $container->getPages()->add($page);

                return $this->redirect()->toRoute(
                    'app/admin/menu/container',
                    [
                        'locale' => $this->locale()->current(),
                        'action' => 'edit',
                        'id'     => $container->getId(),
                    ]
                );  
            }
        }

        return [
            'form'      => $form,
            'container' => $container,
        ];
    }

    /**
     * Gets url form
     *
     * @return MenuUrlForm
     */
    public function getUrlForm()
    {
        return $this->formUrl;
    }

    /**
     * Sets url form
     *
     * @param   MenuUrlForm $form
     * @return  self
     */
    public function setUrlForm(MenuUrlForm $form)
    {
        $this->formUrl = $form;

        return $this;
    }

    /**
     * Gets url form
     *
     * @return MenuUrlForm
     */
    public function getEmptyElementForm()
    {
        return $this->formEmpty;
    }

    /**
     * Sets url form
     *
     * @param   MenuUrlForm $form
     * @return  self
     */
    public function setEmptyElementForm(MenuEmptyForm $form)
    {
        $this->formEmpty = $form;

        return $this;
    }
}