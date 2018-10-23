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
use Soccer\Entity\ClubPlayer;
use Soccer\Entity\Player;
use Soccer\Form\ClubPlayersForm;
use Soccer\Form\Fieldset\TransferForm;

/**
 * ClubPlayerManageController
 */
class ClubPlayerManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var ClubPlayersForm
     */
    protected $clubPlayersForm;

    /**
     * @var TransferForm
     */
    protected $transferForm;

    /**
     * ClubPlayerManageController constructor.
     *
     * @param ClubPlayersForm $clubPlayersForm
     * @param TransferForm    $transferForm
     */
    public function __construct(ClubPlayersForm $clubPlayersForm, TransferForm $transferForm)
    {
        $this->clubPlayersForm = $clubPlayersForm;
        $this->transferForm = $transferForm;
    }


    public function addAction()
    {
        $clubsRepository = $this->getObjectManager()->getRepository(Club::class);

        if (!($this->params('id') && ($club = $clubsRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->clubPlayersForm->setClub($club);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $clubPlayer = new ClubPlayer();
                $data       = $form->getData();

                if ($data['player']->getStatus() !== Player::STATUS_END) {
                    if ($data['player']) {
                        $player = $data['player'];

                        $this->getObjectManager()->refresh($data['player']);
                    }

                    if ($data['player']->getClubs() instanceof ClubPlayer) {
                        $clubPlayer = $data['player']->getClubs();
                    }

                    $player->setStatus(Player::STATUS_PLAYING);

                    $clubPlayer->setClub($club)
                        ->setPlayer($data['player'])
                        ->setNumber($data['number'])
                        ->setPosition($data['position'])
                        ->setSince(\DateTime::createFromFormat('d.m.Y', $data['since']));

                    if ($data['until']) {
                        $clubPlayer->setUntil(\DateTime::createFromFormat('d.m.Y', $data['until']));
                    }

                    if (!$clubPlayer->getId()) {
                        $this->getObjectManager()->persist($clubPlayer);
                    }

                    $this->flashMessenger()
                        ->addSuccessMessage(
                            $this->translate("New club player added")
                        );

                    return $this->redirect()
                        ->toRoute(
                            'app/admin/soccer/club',
                            [
                                'action' => 'players',
                                'id'     => $club->getId(),
                            ],
                            true
                        );
                } else {
                    $form->get('player')
                        ->setMessages(
                            [
                                'ended_career' => 'You trying to select player that has ended his/her career',
                            ]
                        );
                }
            }
        }

        return [
            'club' => $club,
            'form' => $form,
        ];
    }

    public function editAction()
    {
        $clubPlayersRepository = $this->getObjectManager()->getRepository(ClubPlayer::class);

        if (!($this->params('id') && $clubPlayer = $clubPlayersRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->clubPlayersForm->setClub($clubPlayer->getClub());
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            $form->getInputFilter()->get('player')->setRequired(false);

            if ($form->isValid()) {
                $oldClub = $clubPlayer->getClub();
                $data    = $form->getData();

                $clubPlayer
                    ->setNumber($data['number'])
                    ->setPosition($data['position'])
                    ->setSince(\DateTime::createFromFormat('d.m.Y', $data['since']));

                if ($data['until']) {
                    $clubPlayer->setUntil(\DateTime::createFromFormat('d.m.Y', $data['until']));
                }

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate("Changes saved")
                    );

                return $this->redirect()
                    ->toRoute(
                        'app/admin/soccer/club',
                        [
                            'action' => 'edit',
                            'id'     => $oldClub->getId(),
                        ],
                        true
                    );
            }
        } else {
            $form->setData(
                [
                    'number'    => $clubPlayer->getNumber(),
                    'position'  => $clubPlayer->getPosition(),
                    'since'     => $clubPlayer->getSince() ? $clubPlayer->getSince()->format('d.m.Y') : null,
                    'until'     => $clubPlayer->getUntil() ? $clubPlayer->getUntil()->format('d.m.Y') : null,
                ]
            );
        }

        return [
            'form'       => $form,
            'clubPlayer' => $clubPlayer,
            'club'       => $clubPlayer->getClub(),
        ];
    }

    public function transferAction()
    {
        /**
         * @var ClubPlayer $clubPlayer
         */
        $clubPlayersRepository = $this->getObjectManager()->getRepository(ClubPlayer::class);

        if (!($this->params('id') && ($clubPlayer = $clubPlayersRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->transferForm;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $oldClub = $clubPlayer->getClub();
                $data    = $form->getData();
                $to      = new ClubPlayer();

                $to
                    ->setClub($data['club'])
                    ->setNumber($data['number'])
                    ->setPosition($data['position'])
                    ->setSince(\DateTime::createFromFormat('d.m.Y', $data['date']));

                $clubPlayer->setPlaying(false);

                $this->getObjectManager()->persist($to);

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate("Player transferred")
                    );

                return $this->redirect()
                    ->toRoute(
                        'app/admin/soccer/club-player',
                        [
                            'action' => 'edit',
                            'id'     => $clubPlayer->getId(),
                        ],
                        true
                    );
            }
        } else {
            $form->setData(
                [
                    'number'   => $clubPlayer->getNumber(),
                    'position' => $clubPlayer->getPosition(),
                    'date'     => date('d.m.Y')
                ]
            );
        }

        return [
            'form'       => $form,
            'clubPlayer' => $clubPlayer,
            'club'       => $clubPlayer->getClub(),
        ];
    }

    public function deleteAction()
    {
        $clubPlayersRepository = $this->getObjectManager()->getRepository(ClubPlayer::class);

        if (!($this->params('id') && $clubPlayer = $clubPlayersRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();

        if ($request->getQuery('confirm')) {
            $this->getObjectManager()->remove($clubPlayer);

            $this->flashMessenger()
                ->addInfoMessage(
                    $this->translate("Club player was removed")
                );

            return $this->redirect()
                ->toRoute(
                    'app/admin/soccer/club',
                    [
                        'action' => 'players',
                        'id'     => $clubPlayer->getClub()->getId(),
                    ],
                    true
                );
        }

        return [
            'clubPlayer' => $clubPlayer,
            'club'       => $clubPlayer->getClub(),
        ];
    }

    public function endedCareerAction()
    {
        /**
         * @var Player $player
         */
        $clubPlayersRepository = $this->getObjectManager()->getRepository(ClubPlayer::class);

        if (!($this->params('id') && $clubPlayer = $clubPlayersRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $player = $this->getObjectManager()->getRepository(Player::class)->find(
            $clubPlayer->getPlayer()->getId()
        );

        $this->getObjectManager()->refresh($player);

        $player->setStatus(Player::STATUS_END);

        /**
         * @var ClubPlayer $played
         */
        foreach ($player->getClubs() as $played) {
            $played->setPlaying(false);
        }

        $this->getObjectManager()->flush();

        $this->flashMessenger()
            ->addInfoMessage(
                $this->translate(
                    sprintf(
                        "Registered end of career for: %s %s",
                        $clubPlayer->getPlayer()->getFirstName($this->locale()->current()),
                        $clubPlayer->getPlayer()->getLastName($this->locale()->current())
                    )
                )
            );

        return $this->redirect()
            ->toRoute(
                'app/admin/soccer/club',
                [
                    'action' => 'players',
                    'id'     => $clubPlayer->getClub()->getId(),
                ],
                true
            );
    }
}