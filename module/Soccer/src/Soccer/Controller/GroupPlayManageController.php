<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Stage;
use Soccer\Form\GroupPlayForm;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * GroupPlayManageController
 */
class GroupPlayManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var GroupPlayForm
     */
    protected $groupPlayForm;

    /**
     * GroupPlayManageController constructor.
     *
     * @param GroupPlayForm $groupPlayForm
     */
    public function __construct(GroupPlayForm $groupPlayForm)
    {
        $this->groupPlayForm = $groupPlayForm;
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param Stage $stage
     * @return self
     */
    public function setStage(Stage $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $event)
    {
        /**
         * @var Stage $stage
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('stage');

        if (!$stage
            || !($stage = $stagesR->find($stage))
            || $stage->getType() != Stage::TYPE_GROUP
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->setStage($stage);

        $event->getApplication()
            ->getEventManager()
            ->attach(
                MvcEvent::EVENT_RENDER,
                [
                    $this,
                    'onRender',
                ]
            );

        parent::onDispatch($event);
    }

    public function onRender(MvcEvent $event)
    {
        $layout = $event->getViewModel();

        /**
         * @var ViewModel $layout
         * @var ViewModel $childView
         */
        foreach ($layout as $childView) {
            $childView->setVariable('stage', $this->getStage());
        }
    }

    public function indexAction()
    {
        return [];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->groupPlayForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data  = $form->getData();
                $subStage = new Stage();

                $subStage
                    ->setLabel($data['label'])
                    ->setType(Stage::TYPE_SUB_STAGE)
                    ->setParent($this->getStage());

                $this->getObjectManager()->persist($subStage);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('New group play was added')
                    );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                    ],
                    true
                );
            }
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        /**
         * @var Stage $subStage
         */
        $request  = $this->getRequest();
        $form     = $this->groupPlayForm;
        $stagesR  = $this->getObjectManager()->getRepository(Stage::class);
        $subStage = $this->params('id');

        if (!$subStage
            || !($subStage = $stagesR->find($subStage))
            || $subStage->getType() != Stage::TYPE_SUB_STAGE
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $subStage->setLabel($data['label']);

                $this->flashMessenger()->addInfoMessage(
                    $this->translate('Changes was saved')
                );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                    ],
                    true
                );
            }
        } else {
            $form->setData(
                [
                    'label' => $subStage->getLabel(),
                ]
            );
        }

        return [
            'form'      => $form,
            'subStage'  => $subStage,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Stage $subStage
         */
        $stagesR  = $this->getObjectManager()->getRepository(Stage::class);
        $subStage = $this->params('id');

        if (!$subStage
            || !($subStage = $stagesR->find($subStage))
            || $subStage->getType() != Stage::TYPE_SUB_STAGE
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($subStage);

            $this->flashMessenger()->addWarningMessage(
                $this->translate('Item was deleted')
            );

            return $this->redirect()->toRoute(
                null,
                [
                    'action' => 'index',
                ],
                true
            );
        }

        return [
            'subStage' => $subStage,
        ];
    }
}