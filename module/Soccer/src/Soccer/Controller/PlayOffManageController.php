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
use Soccer\Form\PlayOffForm;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * PlayOffManageController
 */
class PlayOffManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var PlayOffForm
     */
    protected $playOffForm;

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * PlayOffManageController constructor.
     *
     * @param PlayOffForm $playOffForm
     */
    public function __construct(PlayOffForm $playOffForm)
    {
        $this->playOffForm = $playOffForm;
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
            || $stage->getType() != Stage::TYPE_PLAY_OFF
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->setStage($stage);

        parent::onDispatch($event);

        $event->getApplication()->getEventManager()
            ->attach(
                MvcEvent::EVENT_RENDER,
                [
                    $this,
                    'onRender',
                ]
            );
    }

    /**
     * Mvc 'Render' event listener
     * Assigns common vars for view model
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        /**
         * @var ViewModel $layout
         * @var ViewModel $childView
         */
        $layout = $event->getViewModel();

        foreach ($layout as $childView) {
            $childView->setVariable(
                'stage',
                $this->getStage()
            );
        }
    }

    public function indexAction()
    {
        return [];
    }

    public function addAction()
    {
        $form = $this->playOffForm;
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $data    = $form->getData();
                $playOff = new Stage();
                
                $playOff->setType(Stage::TYPE_PLAY_OFF_SUB_STAGE)
                    ->setLabel($data['label'])
                    ->setParent($this->getStage());

                $this->getObjectManager()->persist($playOff);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('New play-off stage was added')
                    );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'action' => 'index',
                            'id'     => null,
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
         * @var Stage $playOff
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $playOff = $this->params('id');

        if (!$playOff
            || !($playOff = $stagesR->find($playOff))
            || $playOff->getType() != Stage::TYPE_PLAY_OFF_SUB_STAGE
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->playOffForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $playOff->setLabel($data['label']);

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate('Changes was saved')
                    );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'action' => 'index',
                            'id'     => null,
                        ],
                        true
                    );
            }
        } else {
            $form->setData(
                [
                    'label' => $playOff->getLabel(),
                ]
            );
        }

        return [
            'form'    => $form,
            'playOff' => $playOff,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Stage $playOff
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $playOff = $this->params('id');

        if (!$playOff
            || !($playOff = $stagesR->find($playOff))
            || $playOff->getType() != Stage::TYPE_PLAY_OFF_SUB_STAGE
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($playOff);

            $this->flashMessenger()
                ->addWarningMessage(
                    $this->translate('Item was deleted')
                );

            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'action' => 'index',
                        'id'     => null,
                    ],
                    true
                );
        }

        return [
            'playOff' => $playOff,
        ];
    }
}