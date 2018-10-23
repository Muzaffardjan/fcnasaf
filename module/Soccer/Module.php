<?php
/**
 * Soccer module
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer;

use Admin\WebsiteConfig\TemplatePositionsConfig;
use Doctrine\ORM\EntityManager;
use Soccer\Controller\ClubManageController;
use Soccer\Controller\ClubPlayerManageController;
use Soccer\Controller\GroupPlayManageController;
use Soccer\Controller\MatchDetailsManageController;
use Soccer\Controller\MatchesManageController;
use Soccer\Controller\MatchEventsController;
use Soccer\Controller\PlayerManageController;
use Soccer\Controller\PlayerPositionsController;
use Soccer\Controller\PlayOffManageController;
use Soccer\Controller\SeasonsManageController;
use Soccer\Controller\SeriesManageController;
use Soccer\Controller\StadiumsManageController;
use Soccer\Controller\StaffGroupController;
use Soccer\Controller\StaffGroupManageController;
use Soccer\Controller\StaffManageController;
use Soccer\Controller\StagesManageController;
use Soccer\Controller\TournamentManageController;
use Soccer\Controller\ToursManageController;
use Soccer\Form\ClubForm;
use Soccer\Form\ClubPlayersForm;
use Soccer\Form\CommentMatchEventForm;
use Soccer\Form\Fieldset\TransferForm;
use Soccer\Form\GroupPlayForm;
use Soccer\Form\MatchesFilterForm;
use Soccer\Form\MatchesForm;
use Soccer\Form\MatchLineUpForm;
use Soccer\Form\MatchStatisticsForm;
use Soccer\Form\PlayerCardForm;
use Soccer\Form\PlayerPositionsForm;
use Soccer\Form\PlayersForm;
use Soccer\Form\PlayOffForm;
use Soccer\Form\SeasonsForm;
use Soccer\Form\SeriesForm;
use Soccer\Form\StadiumsForm;
use Soccer\Form\StaffForm;
use Soccer\Form\StaffGroupForm;
use Soccer\Form\StaffGroupMenuForm;
use Soccer\Form\StagesForm;
use Soccer\Form\TournamentsForm;
use Soccer\Form\ToursForm;
use Soccer\View\Helper\PlayerCardsHelper;
use Soccer\View\Helper\SoccerCupBracket;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Listeners
use Soccer\Listener\MenuListener;

class Module implements AutoloaderProviderInterface
{
    const EVENT_MATCH_EVENT_ADDED = 'soccer.match.event.added';

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'invokables' => [
                'Soccer\Controller\StaffGroup'  => StaffGroupController::class,
                'Soccer\Controller\MatchEvents' => MatchEventsController::class,
            ],
            'factories' => [
                'Soccer\Controller\StaffManage' => function ($cm) {
                    return new StaffManageController(
                        $cm->getServiceLocator()->get(StaffForm::class)
                    );
                },
                'Soccer\Controller\StaffGroupManage' => function ($cm) {
                    return new StaffGroupManageController(
                        $cm->getServiceLocator()->get(StaffGroupForm::class),
                        $cm->getServiceLocator()->get(StaffGroupMenuForm::class)
                    );
                },
                'Soccer\Controller\ClubManage' => function ($sm) {
                    return new ClubManageController(
                        $sm->getServiceLocator()->get(ClubForm::class)
                    );
                },
                'Soccer\Controller\ClubPlayerManage' => function ($sm) {
                    return new ClubPlayerManageController(
                        $sm->getServiceLocator()->get(ClubPlayersForm::class),
                        $sm->getServiceLocator()->get(TransferForm::class)
                    );
                },
                'Soccer\Controller\PlayerPositions' => function ($sm) {
                    return new PlayerPositionsController(
                        $sm->getServiceLocator()->get(PlayerPositionsForm::class)
                    );
                },
                'Soccer\Controller\PlayerManage' => function ($sm) {
                    return new PlayerManageController(
                        $sm->getServiceLocator()->get(PlayersForm::class),
                        $sm->getServiceLocator()->get(PlayerCardForm::class)
                    );
                },
                'Soccer\Controller\TournamentManage' => function ($sm) {
                    return new TournamentManageController(
                        $sm->getServiceLocator()->get(TournamentsForm::class)
                    );
                },
                'Soccer\Controller\SeasonsManage' => function ($sm) {
                    return new SeasonsManageController(
                        $sm->getServiceLocator()->get(SeasonsForm::class)
                    );
                },
                'Soccer\Controller\StagesManage' => function ($sm) {
                    return new StagesManageController(
                        $sm->getServiceLocator()->get(StagesForm::class)
                    );
                },
                'Soccer\Controller\GroupPlayManage' => function ($sm) {
                    return new GroupPlayManageController(
                        $sm->getServiceLocator()->get(GroupPlayForm::class)
                    );
                },
                'Soccer\Controller\PlayOffManage' => function ($sm) {
                    return new PlayOffManageController(
                        $sm->getServiceLocator()->get(PlayOffForm::class)
                    );
                },
                'Soccer\Controller\SeriesManage' => function ($sm) {
                    return new SeriesManageController(
                        $sm->getServiceLocator()->get(SeriesForm::class)
                    );
                },
                'Soccer\Controller\ToursManage' => function ($sm) {
                    return new ToursManageController(
                        $sm->getServiceLocator()->get(ToursForm::class)
                    );
                },
                'Soccer\Controller\StadiumsManage' => function ($sm) {
                    return new StadiumsManageController(
                        $sm->getServiceLocator()->get(StadiumsForm::class)
                    );
                },
                'Soccer\Controller\MatchesManage' => function ($sm) {
                    return new MatchesManageController(
                        $sm->getServiceLocator()->get(MatchesForm::class),
                        $sm->getServiceLocator()->get(MatchesFilterForm::class)
                    );
                },
                'Soccer\Controller\MatchDetailsManage' => function ($sm) {
                    $instance = new MatchDetailsManageController(
                        $sm->getServiceLocator()->get(MatchLineUpForm::class)
                    );

                    $instance->setMatchStatisticsForm(
                        $sm->getServiceLocator()->get(MatchStatisticsForm::class)
                    );

                    return $instance;
                },
                'Soccer\Controller\Match' => function ($sm) {
                    return new Controller\MatchController();
                }
            ],
        ];
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'invokables' => [
                MenuListener::class         => MenuListener::class,
                StaffForm::class            => StaffForm::class,
                StaffGroupForm::class       => StaffGroupForm::class,
                StaffGroupMenuForm::class   => StaffGroupMenuForm::class,
                PlayerCardForm::class       => PlayerCardForm::class,
            ],
            'factories' => [
                PlayerPositionsForm::class => function ($sm) {
                    return new PlayerPositionsForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales']
                        ]
                    );
                },
                PlayersForm::class => function ($sm) {
                    return new PlayersForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales']
                        ]
                    );
                },
                ClubForm::class => function ($sm) {
                    return new ClubForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales']
                        ]
                    );
                },
                ClubPlayersForm::class => function ($sm) {
                    return new ClubPlayersForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale'  => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                TransferForm::class => function ($sm) {
                    return new TransferForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                TournamentsForm::class => function ($sm) {
                    return new TournamentsForm(
                        null,
                        ['locales' => $sm->get('config')['translator']['locales'],]
                    );
                },
                SeasonsForm::class => function ($sm) {
                    return new SeasonsForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                StagesForm::class => function ($sm) {
                    return new StagesForm(
                        null,
                        ['locales' => $sm->get('config')['translator']['locales']]
                    );
                },
                GroupPlayForm::class => function ($sm) {
                    return new GroupPlayForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                PlayOffForm::class => function ($sm) {
                    return new PlayOffForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                SeriesForm::class => function ($sm) {
                    return new SeriesForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                ToursForm::class => function ($sm) {
                    return new ToursForm(
                        null,
                        ['locales' => $sm->get('config')['translator']['locales']]
                    );
                },
                StadiumsForm::class => function ($sm) {
                    return new StadiumsForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                MatchesForm::class => function ($sm) {
                    return new MatchesForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                MatchesFilterForm::class => function ($sm) {
                    return new MatchesFilterForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                MatchLineUpForm::class => function ($sm) {
                    return new MatchLineUpForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                MatchStatisticsForm::class => function ($sm) {
                    return new MatchStatisticsForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale' => $sm->get('translator')->getLocale(),
                        ]
                    );
                },
                CommentMatchEventForm::class => function ($sm) {
                    return new CommentMatchEventForm(
                        null,
                        [
                            'locales' => $sm->get('config')['translator']['locales'],
                            'locale'  => $sm->get('translator')->getLocale()
                        ]
                    );
                },
                Form\PeriodMatchEventForm::class => function ($sm) {
                    return new Form\PeriodMatchEventForm();
                }
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'playerCards' => function ($hpm) {
                    $instance = new PlayerCardsHelper(
                        $hpm->getServiceLocator()
                        ->get(TemplatePositionsConfig::class)
                    );

                    $instance->setObjectManager(
                        $hpm->getServiceLocator()
                        ->get(EntityManager::class)
                    );

                    return $instance;
                },
                'soccerCupBracket' => function ($hpm) {
                    $instance = new SoccerCupBracket();

                    $instance->setObjectManager(
                        $hpm->getServiceLocator()
                        ->get('Doctrine\ORM\EntityManager')
                    );

                    return $instance;
                },
            ],
        ];
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            $e->getApplication()->getServiceManager()->get(MenuListener::class)
        );
    }
}
