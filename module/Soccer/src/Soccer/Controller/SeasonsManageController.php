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
use Soccer\Entity\Club;
use Soccer\Entity\Season;
use Soccer\Entity\Stage;
use Soccer\Entity\Tournament;
use Soccer\Form\SeasonsForm;
use Zend\Mvc\MvcEvent;

/**
 * SeasonsController
 */
class SeasonsManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var SeasonsForm
     */
    protected $seasonsForm;

    /**
     * @var Tournament
     */
    protected $tournament;

    /**
     * SeasonsController constructor.
     *
     * @param SeasonsForm $seasonsForm
     */
    public function __construct(SeasonsForm $seasonsForm)
    {
        $this->seasonsForm = $seasonsForm;
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

    public function onDispatch(MvcEvent $event)
    {
        if ($this->params('tournament')) {
            /**
             * @var Tournament|null $tournament
             */
            $tournament = $this->getObjectManager()->getRepository(Tournament::class)
                ->find($this->params('tournament'));

            if ($tournament) {
                $this->setTournament($tournament);
            }
        }

        if (!isset($tournament) || !$tournament instanceof Tournament) {
            $this->getResponse()->setStatusCode(404);
        }

        parent::onDispatch($event);
    }

    public function indexAction()
    {
        return [
            'tournament' => $this->getTournament(),
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->seasonsForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();
                $season = new Season();

                $season->setTournament($this->getTournament())
                    ->setLabel($data['label'])
                    ->setVisible((bool) $data['visible'])
                    ->setType($data['type']);

                if ($season->getType() == Season::TYPE_LEAGUE || $season->getType() == Season::TYPE_EXHIBITION) {
                    $leagueStage = new Stage();
                    $leagueStage->setSeason($season)
                        ->setLabel($season->getTournament()->getId() .'_'. $season->getId())
                        // to make decision
                        ->setWinsBy($data['winsBy'])
                        // league has only one stage
                        ->setType(Stage::TYPE_SINGLE);
                    
                    $season->getStages()
                        ->add($leagueStage);

                    $this->getObjectManager()->persist($leagueStage);
                } elseif ($season->getType() == Season::TYPE_CUP) {
                    $playOffStage = new Stage();

                    $playOffStage->setType(Stage::TYPE_PLAY_OFF);

                    $playOffStage->setSeason($season);

                    $this->getObjectManager()->persist($playOffStage);
                }

                /**
                 * @var Club $club
                 */
                $participants = $season->getClubs();

                foreach ($data['clubs'] as $club) {
                    $participants->add($club);
                }

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate(
                            sprintf(
                                'New season added to %s',
                                $this->getTournament()->getLabel($this->locale()->current())
                            )
                        )
                    );

                $this->getObjectManager()->persist($season);

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale'     => $this->locale()->current(),
                            'tournament' => $this->getTournament()->getId(),
                        ]
                    );
            }
        } else {
            $form->get('visible')->setValue(true);
        }

        return [
            'form'       => $form,
            'tournament' => $this->getTournament(),
        ];
    }

    public function editAction()
    {
        $request           = $this->getRequest();
        $seasonsRepository = $this->getObjectManager()->getRepository(Season::class);

        /**
         * @var Season $season
         */
        if (!$this->params('id') || !($season = $seasonsRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->seasonsForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            $form->getInputFilter()->get('type')
                ->setRequired(false);

            if ($form->isValid()) {
                $data = $form->getData();

                $season->setLabel($data['label'])
                    ->setVisible((bool) $data['visible']);

                if ($season->getType() === Season::TYPE_LEAGUE) {
                    /**
                     * @var Stage $stage
                     */
                    $stage = $season->getStages()->first();

                    if ($stage instanceof Stage) {
                        $stage->setWinsBy($data['winsBy']);
                    }
                }

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate('Changes saved')
                    );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale'     => $this->locale()->current(),
                            'tournament' => $this->getTournament()->getId(),
                        ]
                    );
            }
        } else {
            $clubs = [];

            foreach ($season->getClubs() as $club) {
                $clubs[] = $club->getId();
            }

            $form->setData(
                [
                    'label'  => $season->getLabel(),
                    'type'   => $season->getType(),
                    'visible'=> (bool) $season->isVisible(),
                    'clubs'  => $clubs
                ]
            );
        }

        // Season type is unchangeable
        $form->get('type')->setAttribute('disabled', true);

        return [
            'form'       => $form,
            'season'     => $season,
            'tournament' => $this->getTournament(),
        ];
    }

    public function deleteAction()
    {
        $seasonsRepository = $this->getObjectManager()->getRepository(Season::class);

        /**
         * @var Season $season
         */
        if (!$this->params('id') || !($season = $seasonsRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($season);

            $this->flashMessenger()
                ->addWarningMessage(
                    $this->translate('Item was deleted')
                );

            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'locale'     => $this->locale()->current(),
                        'tournament' => $this->getTournament()->getId(),
                    ]
                );
        }

        return [
            'season'     => $season,
            'tournament' => $this->getTournament(),
        ];
    }
}