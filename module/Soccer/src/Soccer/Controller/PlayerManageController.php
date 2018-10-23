<?php
/**
 * PlayerManageController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Controller;


use Admin\Controller\AbstractObjectManagerAwareController;
use ImagesManager\Controller\ManagerController;
use Soccer\Entity\ClubPlayer;
use Soccer\Entity\Player;
use Soccer\Form\PlayerCardForm;
use Soccer\Form\PlayersForm;
use Zend\Paginator\Paginator;
use Zend\Uri\UriFactory;
use Zend\View\Model\JsonModel;

class PlayerManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var PlayersForm
     */
    protected $playersForm;

    /**
     * @var PlayerCardForm
     */
    protected $playerCardForm;

    /**
     * PlayerManageController constructor.
     *
     * @param PlayersForm $playersForm
     */
    public function __construct(PlayersForm $playersForm, PlayerCardForm $playerCardForm)
    {
        $this->playersForm = $playersForm;
        $this->playerCardForm = $playerCardForm;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $playersRepository = $this->getObjectManager()->getRepository(Player::class);
        $defaultLimit = 10;
        $players = new Paginator(
            $playersRepository->findAllPaginated(
                [
                    $request->getQuery('sort') ? 'player.'.$request->getQuery('sort') : 'player.id' => strtoupper($request->getQuery('order', 'desc'))
                ]
            )
        );

        if ($request->isXmlHttpRequest()) {
            $players->setItemCountPerPage($request->getQuery('limit', $defaultLimit))
                ->setCurrentPageNumber(($request->getQuery('offset', 0) / $players->getItemCountPerPage()) + 1);

            $array = [];

            foreach ($players as $player) {
                $item = [
                    'id'            => $player->getId(),
                    'alias'         => $player->getAlias(),
                    'firstName'     => $player->getFirstName(),
                    'lastName'      => $player->getLastName(),
                    'thirdName'     => $player->getThirdName(),
                    'height'        => $player->getHeight(),
                    'weight'        => $player->getWeight(),
                    'playing'       => null,
                ];

                if ($player->getClubs() && $player->getClubs()->count()) {
                    /**
                     * @var ClubPlayer $clubPlayer
                     */
                    foreach ($player->getClubs() as $clubPlayer) {
                        if (!$clubPlayer->isPlaying()) {
                            continue;
                        }

                        $item['clubs'][] = [
                            'name' => $clubPlayer->getClub()->getName($this->locale()->current()),
                            'id'   => $clubPlayer->getClub()->getId()
                        ];
                    }
                }

                $array[] = $item;
            }

            return new JsonModel(
                [
                    'total' => $players->getTotalItemCount(),
                    'rows'  => $array,
                ]
            );
        } else {
            $players->setCurrentPageNumber($request->getQuery('page', 1))
                ->setItemCountPerPage($defaultLimit);
        }

        return [
            'players' => $players,
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->playersForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $player = new Player();
                $data   = $form->getData();

                $player->setAlias($data['alias'])
                    ->setFirstName($data['firstname'])
                    ->setLastName($data['lastname'])
                    ->setThirdName($data['thirdname'])
                    ->setBirthDate(\DateTime::createFromFormat('d.m.Y', $data['birthDate']))
                    ->setHeight($data['height'])
                    ->setWeight($data['weight'])
                    ->setCountry($data['country'])
                    ->setStatus(Player::STATUS_FREE);

                $this->getObjectManager()->persist($player);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate("New player added")
                );

                if ($request->getQuery('return')) {
                    $uri   = UriFactory::factory($request->getQuery('return'));
                    $query = $uri->getQueryAsArray();

                    $this->setNeedFlush(false);
                    $this->getObjectManager()->flush();

                    $query[$request->getQuery('key', 'key')] = $player->getId();

                    return $this->redirect()->toUrl($uri->toString());
                } else {
                    return $this->redirect()->toRoute(
                        null,
                        ['locale' => $this->locale()->current()]
                    );
                }
            }
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        $playersRepository = $this->getObjectManager()->getRepository(Player::class);

        if (!(($this->params('id') && ($player = $playersRepository->find($this->params('id')))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->playersForm->setUpdate(true);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $player->setAlias($data['alias'])
                    ->setFirstName($data['firstname'])
                    ->setLastName($data['lastname'])
                    ->setThirdName($data['thirdname'])
                    ->setBirthDate(\DateTime::createFromFormat('d.m.Y', $data['birthDate']))
                    ->setHeight($data['height'])
                    ->setWeight($data['weight'])
                    ->setCountry($data['country']);

                if ($data['ended_career']) {
                    $player->setStatus(Player::STATUS_END);
                } elseif ($player->getClubs()->count()) {
                    $player->setStatus(Player::STATUS_PLAYING);
                }

                $this->flashMessenger()
                    ->addInfoMessage($this->translate("Changes saved"));

                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );
            }
        } else {
            $form->setData(
                [
                    'alias'     => $player->getAlias(),
                    'firstname' => $player->getFirstName(),
                    'lastname'  => $player->getLastName(),
                    'thirdname' => $player->getThirdName(),
                    'birthDate' => $player->getBirthDate()->format('d.m.Y'),
                    'height'    => $player->getHeight(),
                    'weight'    => $player->getWeight(),
                    'country'   => $player->getCountry(),
                ]
            );
        }

        return [
            'form'   => $form,
            'player' => $player,
        ];
    }

    public function editCardAction()
    {
        $playersRepository = $this->getObjectManager()->getRepository(Player::class);

        if (!(($this->params('id') && ($player = $playersRepository->find($this->params('id')))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $photos  = $player->getPhotos();

        if ($request->getQuery('change') || !isset($photos['card']) || !$photos['card']) {
            if ($this->apiCall()->isReturned()) {
                $result = $this->apiCall()->getResult();
                $photos['card'] = $result['thumbnails']['soccer_player_card'][0]['href'];

                $player->setPhotos($photos);

                return $this->redirect()
                    ->toRoute(
                        null,
                        [],
                        [
                            'query' => []
                        ],
                        true
                    );
            }

            if ($this->apiCall()->isCancelled()) {
                if (!isset($photos['card'])) {
                    return $this->redirect()
                        ->toRoute(
                            null,
                            [
                                'action' => 'editCard',
                                'id'     => $player->getId(),
                            ],
                            true
                        );
                } else {
                    return $this->redirect()
                        ->toRoute(
                            null,
                            [],
                            [
                                'query' => []
                            ],
                            true
                        );
                }
            }

            return $this->apiCall()->call(
                $this->url()->fromRoute(
                    'app/admin/images-manager',
                    [
                        'locale' => $this->locale()->current(),
                    ]
                ),
                [
                    'mode'       => ManagerController::LISTER_MODE_SELECT_IMAGE,
                    'thumbnails' => [
                        'soccer_player_card',
                    ],
                ]
            );
        }

        $settings = $player->getSettings();
        $form     = $this->playerCardForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $settings['card'] = $form->getData();

                $player->setSettings($settings);

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate("Card settings applied")
                    );

                return $this->redirect()->toRoute(
                    'app/admin/soccer/club',
                    [
                        'action' => 'players',
                        'id'     => $player->getClubs()->first()->getId(),
                    ],
                    true
                );
            }
        } elseif (isset($settings['card'])) {
            $form->setData($settings['card']);
        } else {
            $form->setData(
                [
                    'width'  => 360,
                    'height' => 360,
                    'top'    => 0,
                    'left'   => 0,
                ]
            );
        }

        return [
            'player' => $player,
            'form'   => $form,
        ];
    }

    public function changeProfilePhotoAction()
    {
        $playersRepository = $this->getObjectManager()->getRepository(Player::class);

        if (!(($this->params('id') && ($player = $playersRepository->find($this->params('id')))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->apiCall()->isReturned()) {
            $photos = $player->getPhotos();
            $result = $this->apiCall()->getResult();
            $photos['profile_small'] = $result['thumbnails']['soccer_player_profile_small'][0]['href'];
            $photos['profile'] = $result['thumbnails']['soccer_player_profile'][0]['href'];

            $player->setPhotos($photos);

            return $this->redirect()
                ->toRoute(
                    'app/admin/soccer/club',
                    [
                        'action' => 'players',
                        'id'     => $player->getClubs()->first()->getId(),
                    ],
                    true
                );
        }

        if ($this->apiCall()->isCancelled()) {
            return $this->redirect()
                ->toRoute(
                    'app/admin/soccer/club',
                    [
                        'action' => 'players',
                        'id'     => $player->getClubs()->first()->getId(),
                    ],
                    true
                );
        }

        return $this->apiCall()->call(
            $this->url()->fromRoute(
                'app/admin/images-manager',
                [
                    'locale' => $this->locale()->current(),
                ]
            ),
            [
                'mode'       => ManagerController::LISTER_MODE_SELECT_IMAGE,
                'thumbnails' => [
                    'soccer_player_profile',
                    'soccer_player_profile_small',
                ],
            ]
        );
    }

    public function deleteAction()
    {
        $playersRepository = $this->getObjectManager()->getRepository(Player::class);

        if (!(($this->params('id') && ($player = $playersRepository->find($this->params('id')))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();

        if ($request->getQuery('confirm')) {
            $this->getObjectManager()->remove($player);

            $this->flashMessenger()
                ->addInfoMessage($this->translate("Player was deleted"));

            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );
        }

        return [
            'player' => $player,
        ];
    }
}