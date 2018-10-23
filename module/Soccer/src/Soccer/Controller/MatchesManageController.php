<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Club;
use Soccer\Entity\Match;
use Soccer\Entity\Season;
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Entity\Tour;
use Soccer\Entity\Tournament;
use Soccer\Form\MatchesFilterForm;
use Soccer\Form\MatchesForm;
use Soccer\Repository\MatchesRepository;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;

/**
 * MatchesManageController
 */
class MatchesManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var MatchesForm
     */
    protected $matchesForm;

    /**
     * @var MatchesFilterForm
     */
    protected $matchesFilterForm;

    /**
     * MatchesManageController constructor.
     *
     * @param MatchesForm       $matchesForm
     * @param MatchesFilterForm $matchesFilterForm
     */
    public function __construct(MatchesForm $matchesForm, MatchesFilterForm $matchesFilterForm)
    {
        $this->matchesForm = $matchesForm;
        $this->matchesFilterForm = $matchesFilterForm;
    }

    public function indexAction()
    {
        /**
         * @var MatchesRepository $matchesR
         */
        $matchesR         = $this->getObjectManager()->getRepository(Match::class);
        $request          = $this->getRequest();
        $sort             = null;
        $itemCountPerPage = $request->getQuery('limit', 15);
        $form             = $this->matchesFilterForm;

        if ($request->getQuery('sort')) {
            $sort = [
                $request->getQuery('sort') => strtoupper($request->getQuery('order'))
            ];
        }

        $filter = $request->getQuery()->toArray();

        foreach ($filter as $key => $value) {
            if (!((int) $value)) {
                unset($filter[$key]);
            }
        }

        $form->setData($filter);

        if ($form->isValid()) {
            $data     = $form->getData();
            $matches  = $matchesR->findByPaginated($data, $sort);
        } else {
            $matches = $matchesR->findAllPaginated($sort);
        }

        $matchesPaginator = new Paginator($matches);
        $matchesPaginator->setItemCountPerPage($itemCountPerPage)
            ->setCurrentPageNumber($request->getQuery('page'));

        return [
            'matches' => $matchesPaginator,
            'form' => $form,
        ];
    }
    
    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->matchesForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data  = $form->getData();
                $match = new Match();

                $date = \DateTime::createFromFormat('H:i d.m.Y', $data['time'] . ' '. $data['date']);

                $date->setTime($date->format('H'), $date->format('i'), 0);

                $match->setDate($date)
                    ->setSeason($data['season'])
                    ->setStatus($data['status'])
                    ->setStadium($data['stadium'])
                    ->setHost($data['host'])
                    ->setGuest($data['guest']);

                if ($match->getSeason()->getType() == Season::TYPE_LEAGUE || $match->getSeason()->getType() == Season::TYPE_EXHIBITION) {
                    $match->setStage($match->getSeason()->getStages()->first());
                } else {
                    $match->setStage($data['subStage']);
                }

                if ($data['series'] instanceof Series) {
                    $match->setSeries($data['series']);
                } else {
                    $match->setTour($data['tour']);
                }

                $this->getObjectManager()->persist($match);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('New match added')
                    );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                        'id'     => null,
                    ]
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
         * @var Match $match
         */
        $request  = $this->getRequest();
        $matchesR = $this->getObjectManager()->getRepository(Match::class);
        $match    = $this->params('id');

        if (!($match && $match = $matchesR->find($match))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->matchesForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data  = $form->getData();

                $date = \DateTime::createFromFormat('H:i d.m.Y', $data['time'] . ' '. $data['date']);

                $date->setTime($date->format('H'), $date->format('i'), 0);

                $match->setDate($date)
                    ->setSeason($data['season'])
                    ->setStatus($data['status'])
                    ->setStadium($data['stadium'])
                    ->setHost($data['host'])
                    ->setGuest($data['guest']);

                if ($match->getSeason()->getType() == Season::TYPE_LEAGUE || $match->getSeason()->getType() == Season::TYPE_EXHIBITION) {
                    $match->setStage($match->getSeason()->getStages()->first());
                } else {
                    $match->setStage($data['subStage']);
                }

                if ($data['series'] instanceof Series) {
                    $match->setSeries($data['series']);
                } else {
                    $match->setTour($data['tour']);
                }

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('Changes saved')
                    );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                        'id'     => null,
                    ]
                );
            }
        } else {
            $form->setData(
                [
                    'time'        => $match->getDate()->format('H:i'),
                    'date'        => $match->getDate()->format('d.m.Y'),
                    'tournament'  => is_object($match->getSeason())? $match->getSeason()->getTournament()->getId(): null,
                    'season'      => is_object($match->getSeason())? $match->getSeason()->getId(): null,
                    'status'      => $match->getStatus(),
                    'stadium'     => is_object($match->getStadium())? $match->getStadium()->getId(): null,
                    'host'        => is_object($match->getHost())? $match->getHost()->getId(): null,
                    'guest'       => is_object($match->getGuest())? $match->getGuest()->getId(): null,
                    'subStage'    => is_object($match->getStage())? $match->getStage()->getId(): null,
                    'tour'        => is_object($match->getTour())? $match->getTour()->getId(): null,
                    'series'      => is_object($match->getSeries())? $match->getSeries()->getId(): null,
                ]
            );
        }

        return [
            'form' => $form,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Match $match
         */
        $request  = $this->getRequest();
        $matchesR = $this->getObjectManager()->getRepository(Match::class);
        $match    = $this->params('id');

        if (!($match && $match = $matchesR->find($match))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->getQuery('confirm')) {
            $this->getObjectManager()->remove($match);

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
                    ]
                );
        }

        return [
            'match' => $match,
        ];
    }

    public function fetchSeasonsAction()
    {
        $tournamentsR = $this->getObjectManager()->getRepository(Tournament::class);
        $tournament   = $this->params('id');

        if (!($tournament && $this->getRequest()->isXmlHttpRequest() && $tournament = $tournamentsR->find($tournament))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = [];

        foreach ($tournament->getSeasons() as $season) {
            $response[] = [
                'id'    => $season->getId(),
                'type'  => $season->getType(),
                'label' => $season->getLabel($this->locale()->current())
            ];
        }

        return new JsonModel($response);
    }

    public function fetchStagesAction()
    {
        $seasonsR = $this->getObjectManager()->getRepository(Season::class);
        $season   = $this->params('id');

        if (!($season && $this->getRequest()->isXmlHttpRequest() && $season = $seasonsR->find($season))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = [];

        foreach ($season->getStages() as $stage) {
            $response[] = [
                'id'    => $stage->getId(),
                'type'  => $stage->getType(),
                'label' => $stage->getLabel($this->locale()->current())
            ];
        }

        return new JsonModel($response);
    }

    public function fetchSubStagesAction()
    {
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('id');

        if (!($stage && $this->getRequest()->isXmlHttpRequest() && $stage = $stagesR->find($stage))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = [];

        /**
         * @var Stage $stage
         * @var Stage $subStage
         */
        foreach ($stage->getSubStages() as $subStage) {
            $response[] = [
                'id'    => $subStage->getId(),
                'type'  => $subStage->getType(),
                'label' => $subStage->getLabel($this->locale()->current())
            ];
        }

        return new JsonModel($response);
    }

    public function fetchToursAction()
    {
        /**
         * @var Stage $stage
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('id');

        if (!($stage
            && $this->getRequest()->isXmlHttpRequest()
            && ($stage = $stagesR->find($stage))
            && ($stage->getType() == Stage::TYPE_SUB_STAGE || $stage->getType() == Stage::TYPE_SINGLE)
        )) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = [];

        /**
         * @var Tour  $tour
         */
        foreach ($stage->getTours() as $tour) {
            $response[] = [
                'id'    => $tour->getId(),
                'label' => $tour->getLabel($this->locale()->current())
            ];
        }

        return new JsonModel($response);
    }

    public function fetchSeriesAction()
    {
        /**
         * @var Stage $stage
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('id');

        if (!($stage && $this->getRequest()->isXmlHttpRequest()
            && ($stage = $stagesR->find($stage))
            && $stage->getType() == Stage::TYPE_PLAY_OFF_SUB_STAGE
        )) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = [];

        /**
         * @var Series $series
         */
        foreach ($stage->getSeries() as $series) {
            $response[] = [
                'id'    => $series->getId(),
                'label' => $series->getLabel($this->locale()->current())
            ];
        }

        return new JsonModel($response);
    }

    public function fetchClubsAction()
    {
        $fetch   = ['tour' => 1, 'series' => 2, 'all' => 3];
        $request = $this->getRequest();

        if (!($request->isXmlHttpRequest() && $this->params('id') && in_array($request->getQuery('for'), $fetch))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $clubs = [];

        if ($request->getQuery('for') == 1) {
            // provided id is tour id
            /**
             * @var Tour $tour
             */
            $tour = $this->getObjectManager()->getRepository(Tour::class)
                ->find($this->params('id'));

            if ($tour->getStage()->getType() == Stage::TYPE_SUB_STAGE) {
                $season = $tour->getStage()->getParent()->getSeason();
            } else {
                $season = $tour->getStage()->getSeason();
            }

            $this->getObjectManager()->refresh($season);

            /**
             * @var Club $club
             */
            foreach ($season->getClubs() as $club) {
                $clubs[] = [
                    'id'    => $club->getId(),
                    'name'  => $club->getName($this->locale()->current()),
                ];
            }
        } elseif ($request->getQuery('for') == 2) {
            // provided id is series id
            /**
             * @var Series $series
             */
            $series = $this->getObjectManager()->getRepository(Series::class)
                ->find($this->params('id'));

            if ($series->getFirst() && $series->getSecond()) {
                 $clubs = [
                     [
                         'id'   => $series->getFirst()->getId(),
                         'name' => $series->getFirst()->getName($this->locale()->current()),
                     ],
                     [
                         'id'   => $series->getSecond()->getId(),
                         'name' => $series->getSecond()->getName($this->locale()->current()),
                     ],
                 ];
            } else {
                $season = $series->getStage()->getParent()->getSeason();

                $this->getObjectManager()->refresh($season);

                /**
                 * @var Club $club
                 */
                foreach ($season->getClubs() as $club) {
                    $clubs[] = [
                        'id'    => $club->getId(),
                        'name'  => $club->getName($this->locale()->current()),
                    ];
                }
            }
        } else {
            $all = $this->getObjectManager()->getRepository(Club::class)->findAll();

            /**
             * @var Club $club
             */
            foreach ($all as $club) {
                $clubs[] = [
                    'id'    => $club->getId(),
                    'name'  => $club->getName($this->locale()->current()),
                ];
            }
        }

        return new JsonModel($clubs);
    }
}