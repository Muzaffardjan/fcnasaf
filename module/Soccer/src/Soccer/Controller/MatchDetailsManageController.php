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
use Doctrine\Common\Collections\Criteria;
use Soccer\Entity\Club;
use Soccer\Entity\ClubPlayer;
use Soccer\Entity\LineUp;
use Soccer\Entity\Match;
use Soccer\Entity\MatchStatistics;
use Soccer\Form\MatchLineUpForm;
use Soccer\Form\MatchStatisticsForm;
use Soccer\Repository\ClubPlayersRepository;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * MatchDetailsController
 */
class MatchDetailsManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var Match
     */
    protected $match;

    /**
     * @var MatchLineUpForm
     */
    protected $matchLineUpForm;

    /**
     * @var MatchStatisticsForm
     */
    protected $matchStatisticsForm;

    /**
     * MatchDetailsManageController constructor.
     *
     * @param MatchLineUpForm $matchLineUpForm
     */
    public function __construct(MatchLineUpForm $matchLineUpForm)
    {
        $this->matchLineUpForm = $matchLineUpForm;
    }

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $event)
    {
        $matchesR = $this->getObjectManager()->getRepository(Match::class);
        $match    = $this->params('match');

        if (!($match && ($match = $matchesR->find($match)))) {
            $this->getResponse()->setStatusCode(404);
            return;
        } else {
            $this->setMatch($match);
        }

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
            $childView->setVariable('match', $this->getMatch());
        }
    }

    public function panelAction()
    {
        $matchLineUpForm = $this->matchLineUpForm->setMatch($this->getMatch());

        return [
            'matchLineUpForm' => $matchLineUpForm,
        ];
    }

    public function saveMatchLineUpAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $matchLineUpForm = $this->matchLineUpForm->setMatch($this->getMatch());
        $matchLineUpForm->setData($request->getPost());

        if ($matchLineUpForm->isValid()) {
            $data = $matchLineUpForm->getData();

            if (!$this->getMatch()->getLineUp()->count()) {
                $hostLineUp  = new LineUp();
                $guestLineUp = new LineUp();

                $hostLineUp->setMatch($this->getMatch())
                    ->setClub($this->getMatch()->getHost());
                $guestLineUp->setMatch($this->getMatch())
                    ->setClub($this->getMatch()->getGuest());
            } else {
                $criteria = Criteria::create();
                $expr     = Criteria::expr();

                $hostLineUp  = $this->getMatch()->getLineUp()
                    ->matching(
                        $criteria->andWhere($expr->eq('club', $this->getMatch()->getHost()))
                    )
                    ->first();

                $criteria = Criteria::create();

                $guestLineUp = $this->getMatch()->getLineUp()
                    ->matching(
                        $criteria->andWhere($expr->eq('club', $this->getMatch()->getGuest()))
                    )
                    ->first();
            }

            $hostLineUp->getStarters()->clear();

            foreach ($data['hostStarters'] as $hStarter) {
                $hostLineUp->getStarters()->add($hStarter);
            }

            $hostLineUp->getSubstitutes()->clear();

            foreach ($data['hostSubstitutes'] as $hSubstitute) {
                $hostLineUp->getSubstitutes()->add($hSubstitute);
            }

            $guestLineUp->getStarters()->clear();

            foreach ($data['guestStarters'] as $gStarter) {
                $guestLineUp->getStarters()->add($gStarter);
            }

            $guestLineUp->getSubstitutes()->clear();

            foreach ($data['guestSubstitutes'] as $gSubstitute) {
                $guestLineUp->getSubstitutes()->add($gSubstitute);
            }

            $this->getMatch()->getLineUp()
                ->add($hostLineUp);

            $this->getMatch()->getLineUp()
                ->add($guestLineUp);

            $this->getObjectManager()->persist($hostLineUp);
            $this->getObjectManager()->persist($guestLineUp);

            $this->flashMessenger()
                ->addSuccessMessage($this->translate('Line ups successfully saved'));

            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'action' => 'panel',
                    ],
                    true
                );
        } else {
            $view = new ViewModel();

            $view->setTemplate('soccer/match-details-manage/panel')
                ->setVariable('matchLineUpForm', $matchLineUpForm);

            return $view;
        }
    }

    public function statisticsAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->getMatchStatisticsForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $stat = new MatchStatistics();

                $stat->setMatch($this->getMatch())
                    ->setType($data['type'])
                    ->setLabel($data['label'])
                    ->setHostValue($data['hostValue'])
                    ->setGuestValue($data['guestValue']);

                $this->getObjectManager()->persist($stat);
                $this->getObjectManager()->flush();

                return new JsonModel(
                    [
                        'id'         => $stat->getId(),
                        'type'       => $stat->getType(),
                        'hostValue'  => $stat->getHostValue(),
                        'guestValue' => $stat->getGuestValue(),
                        'label'      => $stat->getLabel(),
                    ]
                );
            }
        }

        if ($request->getQuery('action') == 'delete' && $request->getQuery('id')) {
            $stat = $this->getObjectManager()
                ->getRepository(MatchStatistics::class)
                ->find($request->getQuery('id'));

            if ($stat) {
                $this->getObjectManager()->remove($stat);

                return new JsonModel(['status' => true]);
            } else {
                $this->getResponse()->setStatusCode(404);
                return;
            }
        }

        $view = new ViewModel(
            [
                'form'  => $form,
                'match' => $this->getMatch()
            ]
        );

        $view->setTerminal(true);

        return $view;
    }

    public function getPlayersOfAction()
    {
        $request = $this->getRequest();
        $clubsR  = $this->getObjectManager()->getRepository(Club::class);
        $club    = $request->getQuery('club');

        if (!($request->isXmlHttpRequest() && $club && ($club = $clubsR->find($club)))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        /**
         * @var ClubPlayersRepository $clubPlayersR
         */
        $clubPlayersR = $this->getObjectManager()->getRepository(ClubPlayer::class);

        $result = $clubPlayersR->findGroupedByPositionPlayersOf($club);
        $players = [];

        /**
         * @var ClubPlayer $player
         */
        foreach ($result as $player) {
            $players[$player->getPosition()->getPluralLabel($this->locale()->current())][] = [
                'id'        => $player->getPlayer()->getId(),
                'firstName' => $player->getPlayer()->getFirstName($this->locale()->current()),
                'lastName'  => $player->getPlayer()->getLastName($this->locale()->current()),
                'number'    => $player->getNumber(),
                'position'  => $player->getPosition()->getLabel($this->locale()->current()),
            ];
        }

        return new JsonModel($players);
    }

    /**
     * @return MatchStatisticsForm
     */
    public function getMatchStatisticsForm()
    {
        return $this->matchStatisticsForm;
    }

    /**
     * @param MatchStatisticsForm $matchStatisticsForm
     */
    public function setMatchStatisticsForm(MatchStatisticsForm $matchStatisticsForm)
    {
        $this->matchStatisticsForm = $matchStatisticsForm;
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param Match $match
     * @return $this
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;

        return $this;
    }
}