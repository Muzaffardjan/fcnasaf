<?php 
/**
 * PageController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Controller;

use Zend\View\Model\JsonModel;
use Admin\Controller\AbstractObjectManagerAwareController;
use Menu\Form\PagesForm;

class PageController extends AbstractObjectManagerAwareController
{
    /**
     * @var PagesForm
     */
    protected $formPages;

    public function __construct(PagesForm $formPages)
    {
        $this->formPages = $formPages;
    }

    public function editAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Page'
        );

        if (!($this->params('id') 
            && ($page = $repository->find($this->params('id')))
            && $request->getQuery('return')
            )
        ) {
            $this->getResponse()->setStatusCode(404);
            exit;
        }

        $form = $this->formPages;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $page->setLabel($data['label'])
                ->setTitle($data['title'])
                ->setLocale($data['locale'])
                ->setFragment($data['fragment'])
                ->setTarget($data['target'])
                ->setVisible((bool) $data['visible']);

                if ($page->getUri()) {
                    $page->setUri($data['uri']);
                }

                return $this->redirect()->toUrl(
                    $request->getQuery('return')
                );
            }
        } else {
            $form->setData(
                [
                    'label'    => $page->getLabel(),
                    'title'    => $page->getTitle(),
                    'locale'   => $page->getLocale(),
                    'fragment' => $page->getFragment(),
                    'target'   => $page->getTarget(),
                    'visible'  => $page->getVisible(),
                ]
            );

            if ($page->getUri()) {
                $form->get('url')->setValue($page->getUri());
            }
        }

        return [
            'page'   => $page,
            'form'   => $form,
            'return' => $request->getQuery('return'),
        ];
    }

    public function deleteAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Page'
        );

        if (!($this->params('id') 
            && ($page = $repository->find($this->params('id')))
            && $request->isPost()
            && $request->isXmlHttpRequest()
            )
        ) {
            $this->getResponse()->setStatusCode(404);
            exit;
        }

        $containers = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        )->findAll();

        foreach ($containers as $container) {
            $container->getPages()->removeElement($page);
        }

        $this->getObjectManager()->remove($page);

        return new JsonModel(
            [
                'status' => true,
            ]
        );
    }
}