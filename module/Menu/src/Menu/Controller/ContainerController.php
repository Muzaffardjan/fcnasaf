<?php 
/**
 * ContainerController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Controller;

use Zend\Navigation\Navigation;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Doctrine\Common\Collections\Criteria;
use Admin\Controller\AbstractObjectManagerAwareController;
use Menu\Form\ContainersForm;
use Menu\Entity\Page;
use Menu\Entity\Container;
use Menu\Event\MenuEvent;

class ContainerController extends AbstractObjectManagerAwareController
{
    /**
     * @var array
     */
    private $flattened;

    public function indexAction()
    {
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        return [
            'containers' => $repository->findBy([], ['id' => 'DESC']),
        ];  
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = new ContainersForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $container = new Container();
                $data      = $form->getData();

                $container->setLabel($data['label']);

                $this->getObjectManager()->persist($container);
                $this->getObjectManager()->flush();

                return $this->redirect()->toRoute(
                    null, 
                    [
                        'action' => 'edit',
                        'id'     => $container->getId(),
                    ],
                    true
                );
            }
        }

        return [
            'form' => $form,
        ];  
    }

    public function getActionsModalAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if ($this->params('id')
            && $container = $repository->find($this->params('id')) 
        ) {
            $eventManager = $this->getEvent()
            ->getApplication()
            ->getEventManager();
            $event   = new MenuEvent(MenuEvent::GET_ACTIONS, $container);
            $actions = $eventManager->trigger($event);

            $reverse = [];

            foreach ($actions as $action) {
                $reverse[] = $action;
            }

            $reverse = array_reverse($reverse);

            $view = new ViewModel(
                [
                    'actions' => $reverse,
                ]
            );

            $view->setTerminal(true);

            return $view;
        }

        $this->getResponse()->setStatusCode(404);
        return;        
    }

    public function editAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if ($this->params('id')
            && $container = $repository->find($this->params('id')) 
        ) {
            $form    = new ContainersForm();

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data      = $form->getData();

                    $container->setLabel($data['label']);

                    return $this->redirect()->refresh();
                }
            } else {
                $form->setData(
                    [
                        'label' => $container->getLabel()
                    ]
                );
            }

            // using navigation class as iterator
            $navigation = new Navigation($container->toArray());
             
            return [
                'container' => $container,
                'form'      => $form,
                'navigation'=> $navigation,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function saveChangesAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if ($this->params('id')
            && $request->isXmlHttpRequest()
            && $container = $repository->find($this->params('id'))
        ) {
            $structure = Json::decode($request->getContent(), Json::TYPE_ARRAY);
                
            if (is_array($structure) && !$structure) {
                $manager = $this->getObjectManager();
                foreach ($container->getPages() as $page) {
                    $manager->remove($page);
                }
                
                $container->getPages()->clear();                
            } else if (is_array($structure)) {
                $this->unbound($container);
                $this->boundAs($structure, $container);
            }

            return new JsonModel(
                [
                    'status' => true,
                ]
            );
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if ($this->params('id')
            && $container = $repository->find($this->params('id')) 
        ) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($container);

                $this->flashMessenger()->addWarningMessage(
                    $this->translate('Menu container %s \' \" was deleted')
                );

                return $this->redirect()->toRoute(
                    null, 
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );  
            }
             
            return [
                'container' => $container,
                'referer'   => null
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    private function boundAs(array $structure, $container)
    {
        foreach ($structure as $key => $item) {
            $page = $this->getObjectManager()
            ->getRepository(
                'Menu\Entity\Page'
            )
            ->find($item['id']);

            if ($container instanceof Container) {
                $container->getPages()->add($page);
            } 

            if ($container instanceof Page) {
                $page->setParent($container);
            }

            $page->setOrder($key);

            if (isset($item['children']) && $item['children']) {
                $this->boundAs($item['children'], $page);
            }
        }
    }

    private function unbound($container)
    {
        $pages = $container->getPages();

        foreach ($pages as $page) {
            if ($page instanceof Page) {
                $page->setParent(null)->setOrder(null);
            }

            if ($page->getPages()->count()) {
                $this->unbound($page);
            }
        }

        if ($container instanceof Container) {
            $container->getPages()->clear();
        }
    }
}