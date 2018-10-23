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
use Soccer\Entity\Tour;
use Soccer\Entity\Tournament;
use Soccer\Form\ToursForm;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * TorsManageController
 */
class ToursManageController extends AbstractObjectManagerAwareController
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
     * @var Stage
     */
    protected $stage;

    /**
     * @var ToursForm
     */
    protected $toursForm;

    /**
     * ToursManageController constructor.
     *
     * @param ToursForm $toursForm
     */
    public function __construct(ToursForm $toursForm)
    {
        $this->toursForm = $toursForm;
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

    public function onDispatch(MvcEvent $event)
    {
        $tournamentsR = $this->getObjectManager()->getRepository(Tournament::class);
        $seasonsR     = $this->getObjectManager()->getRepository(Season::class);
        $stageR       = $this->getObjectManager()->getRepository(Stage::class);
        $tournament   = $this->params('tournament');
        $season       = $this->params('season');
        $stage        = $this->params('stage');

        /**
         * @var Tournament $tournament
         * @var Season     $season
         * @var Stage      $stage
         */
        if (!$tournament
            || !$season
            || !($tournament = $tournamentsR->find($tournament))
            || !($season = $seasonsR->find($season))
            || !($season->getType() === Season::TYPE_LEAGUE ||$season->getType() === Season::TYPE_CHAMPIONSHIP)
            || ($season->getType() === Season::TYPE_CHAMPIONSHIP && (!$stage || !($stage = $stageR->find($stage)) || $stage->getType() !== Stage::TYPE_SUB_STAGE))
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Attach handler
        $event->getApplication()->getEventManager()
            ->attach(
                MvcEvent::EVENT_RENDER,
                [
                    $this,
                    'onRender'
                ]
            );

        $this->setSeason($season)
            ->setTournament($tournament);

        if ($stage instanceof Stage) {
            $this->setStage($stage);
        }

        parent::onDispatch($event);
    }

    /**
     * After dispatch pre render event handler
     * Sets common variables for ViewModel
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        $layout = $event->getViewModel();

        /**
         * iterate trough child views of layout and assign common vars
         * we do not need to set vars for layout itself
         *
         * @var ViewModel $layout
         * @var ViewModel $childView
         */
        foreach ($layout as $childView) {
            $childView
                ->setVariable('tournament', $this->getTournament())
                ->setVariable('season', $this->getSeason())
                ->setVariable('stage', $this->getStage());
        }
    }

    public function indexAction()
    {

    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->toursForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $tour = new Tour();

                $tour->setLabel($data['label']);

                if ($this->getStage() instanceof Stage) {
                    $tour->setStage($this->getStage());
                } elseif ($this->getSeason()->getType() === Season::TYPE_LEAGUE
                    && $this->getSeason()->getStages()->first() instanceof Stage
                ) {
                    $tour->setStage($this->getSeason()->getStages()->first());
                }

                $this->getObjectManager()->persist($tour);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('New tour added')
                );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'id'     => null,
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
         * @var Tour $tour
         */
        $request = $this->getRequest();
        $form    = $this->toursForm;
        $toursR  = $this->getObjectManager()->getRepository(Tour::class);
        $tour    = $this->params('id');

        if (!$tour || !($tour = $toursR->find($tour))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $tour->setLabel($data['label']);

                $this->flashMessenger()->addInfoMessage(
                    $this->translate('Changes saved')
                );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'id'     => null,
                        'action' => 'index',
                    ],
                    true
                );
            }
        } else {
            $form->setData(
                [
                    'label' => $tour->getLabel(),
                ]
            );
        }

        return [
            'form' => $form,
            'tour' => $tour,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Tour $tour
         */
        $toursR  = $this->getObjectManager()->getRepository(Tour::class);
        $tour    = $this->params('id');

        if (!$tour || !($tour = $toursR->find($tour))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($tour);

            $this->flashMessenger()->addWarningMessage(
                $this->translate('Item was deleted')
            );

            return $this->redirect()->toRoute(
                null,
                [
                    'id'     => null,
                    'action' => 'index',
                ],
                true
            );
        }

        return [
            'tour' => $tour
        ];
    }
}