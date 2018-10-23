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
use Soccer\Entity\Season;
use Soccer\Entity\Stage;
use Soccer\Entity\Tournament;
use Soccer\Form\StagesForm;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * StagesManageController
 */
class StagesManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var Tournament
     */
    protected $tournament;

    /**
     * @var Season
     */
    protected $season;

    /**
     * @var StagesForm
     */
    protected $stagesForm;

    /**
     * StagesManageController constructor.
     *
     * @param StagesForm $stagesForm
     */
    public function __construct(StagesForm $stagesForm)
    {
        $this->stagesForm = $stagesForm;
    }

    /**
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return self
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     * @return self
     */
    public function setSeason(Season $season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $event)
    {
        /**
         * @var Tournament $tournament
         * @var Season     $season
         */
        $tournamentsR = $this->getObjectManager()->getRepository(Tournament::class);
        $seasonsR     = $this->getObjectManager()->getRepository(Season::class);
        $tournament   = $this->params('tournament');
        $season       = $this->params('season');

        if (!$tournament
            || !$season
            || !($tournament = $tournamentsR->find($tournament))
            || !($season     = $seasonsR->find($season))
            || $season->getType() != Season::TYPE_CHAMPIONSHIP
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->setTournament($tournament)
            ->setSeason($season);

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
            $childView->setVariable('tournament', $this->getTournament())
                ->setVariable('season', $this->getSeason());
        }
    }

    public function indexAction()
    {
        return [];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->stagesForm;

        if ($this->getSeason()->getType() == Season::TYPE_CUP) {
            $form->get('type')
                ->setAttribute('disabled', true)
                ->setValue(Stage::TYPE_PLAY_OFF);
        }

        if ($request->isPost()) {
            $post = $request->getPost()->toArray();

            if ($this->getSeason()->getType() == Season::TYPE_CUP) {
                $post['type'] = Stage::TYPE_PLAY_OFF;
            }

            $form->setData($post);

            if ($form->isValid()) {
                $data  = $form->getData();
                $stage = new Stage();

                $stage->setType($data['type'])
                    ->setLabel($data['label'])
                    ->setSeason($this->getSeason());

                if ($stage->getType() == Stage::TYPE_GROUP) {
                    $stage->setWinsBy($data['winsBy']);
                } elseif ($stage->getType() == Stage::TYPE_PLAY_OFF) {
                    $stage->setMatchesCount($data['matchesCount']);
                }

                $this->getObjectManager()->persist($stage);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('New stage added')
                );

                return $this->redirect()->toRoute(
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
         * @var Stage $stage
         */
        $request = $this->getRequest();
        $form    = $this->stagesForm->editMode();
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('id');

        if (!$stage || !($stage = $stagesR->find($stage))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form->get('type')
            ->setAttribute('disabled', true);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $stage->setLabel($data['label']);

                if ($stage->getType() == Stage::TYPE_GROUP) {
                    $stage->setWinsBy($data['winsBy']);
                } elseif ($stage->getType() == Stage::TYPE_PLAY_OFF) {
                    $stage->setMatchesCount($data['matchesCount']);
                }

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('Changes saved')
                );

                return $this->redirect()->toRoute(
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
                    'type'         => $stage->getType(),
                    'label'        => $stage->getLabel(),
                    'winsBy'       => $stage->getWinsBy(),
                    'matchesCount' => $stage->getMatchesCount(),
                ]
            );
        }

        return [
            'form'  => $form,
            'stage' => $stage,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Stage $stage
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('id');

        if (!$stage || !($stage = $stagesR->find($stage))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($stage);

            $this->flashMessenger()
                ->addWarningMessage(
                    $this->translate('Item was deleted')
                );

            return $this->redirect()->toRoute(
                null,
                [
                    'action' => 'index',
                    'id'     => null,
                ],
                true
            );
        }

        return [
            'stage' => $stage,
        ];
    }
}