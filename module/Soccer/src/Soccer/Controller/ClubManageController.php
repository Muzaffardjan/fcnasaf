<?php
/**
 * ClubManageController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Controller;


use Admin\Controller\AbstractObjectManagerAwareController;
use ImagesManager\Controller\ManagerController;
use Soccer\Entity\Club;
use Soccer\Entity\ClubPlayer;
use Soccer\Entity\Player;
use Soccer\Entity\PlayerPosition;
use Soccer\Form\ClubForm;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;

class ClubManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var ClubForm
     */
    protected $clubForm;

    /**
     * ClubManageController constructor.
     *
     * @param ClubForm $clubForm
     */
    public function __construct(ClubForm $clubForm)
    {
        $this->clubForm = $clubForm;
    }

    public function indexAction()
    {
        $request         = $this->getRequest();
        $clubsRepository = $this->getObjectManager()->getRepository(Club::class);
        $defaultLimit    = 10;
        $clubs           = new Paginator(
            $clubsRepository->findAllPaginated(
                [
                    $request->getQuery('sort') ? 'club.'.$request->getQuery('sort') : 'club.alias' => strtoupper($request->getQuery('order', 'asc'))
                ]
            )
        );

        if ($request->isXmlHttpRequest()) {
            $clubs->setItemCountPerPage($request->getQuery('limit', $defaultLimit))
                ->setCurrentPageNumber(($request->getQuery('offset', 0) / $clubs->getItemCountPerPage()) + 1);

            $array = [];

            foreach ($clubs as $club) {
                $array[] = [
                    'id'            => $club->getId(),
                    'alias'         => $club->getAlias(),
                    'logo'          => $club->getSmallLogo(),
                    'name'          => $club->getName(),
                    'founded'       => $club->getFounded(),
                    'tableName'     => $club->getTableName(),
                    'parentClub'    => [
                        'logo' => $club->getParentClub()? $club->getParentClub()->getSmallLogo(): null,
                        'name' => $club->getParentClub()? $club->getParentClub()->getName(): null,
                    ],
                ];
            }

            return new JsonModel(
                [
                    'total' => $clubs->getTotalItemCount(),
                    'rows'  => $array,
                ]
            );
        } else {
            $clubs->setCurrentPageNumber($request->getQuery('page', 1))
                ->setItemCountPerPage($defaultLimit);
        }

        return [
            'clubs' => $clubs,
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->clubForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $club = new Club();
                $data = $form->getData();

                $club->setAlias($data['alias'])
                    ->setName($data['name'])
                    ->setFounded($data['founded'])
                    ->setTableName($data['tableName'])
                    ->setParentClub($data['parentClub']);

                $this->getObjectManager()
                    ->persist($club);

                $this->setNeedFlush(false);
                $this->getObjectManager()->flush();

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate("New club added")
                );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'action' => 'edit',
                            'id'     => $club->getId(),
                        ],
                        true
                    );
            }
        }

        return [
            'form' => $form,
        ];
    }

    /**
     * @return array|void
     */
    public function editAction()
    {
        $clubsRepository = $this->getObjectManager()->getRepository('Soccer\Entity\Club');

        if (!($this->params('id') && ($club = $clubsRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->clubForm->setUpdate(true);
        
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $clubs = $clubsRepository->findBy(['alias' => $data['alias']]);

                foreach ($clubs as $key => $item) {
                    if ($item->getId() == $club->getId()) {
                        unset($clubs[$key]);
                    }
                }

                if (!($data['parentClub'] && $data['parentClub']->getId() == $club->getId()) && !count($clubs)) {
                    $club->setAlias($data['alias'])
                        ->setName($data['name'])
                        ->setFounded($data['founded'])
                        ->setTableName($data['tableName'])
                        ->setParentClub($data['parentClub']);

                    $this->flashMessenger()->addInfoMessage(
                        $this->translate("Changes saved")
                    );

                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
                } elseif ($data['parentClub'] && $data['parentClub']->getId() == $club->getId()) {
                    $form->get('parentClub')
                        ->setMessages(
                            [
                                'wtf' => 'Entity can not be the parent of itself',
                            ]
                        );
                } elseif (count($clubs)) {
                    $form->get('alias')
                        ->setMessages(
                            [
                                'object_exists' => 'Club with this alias already exists, please choose different one',
                            ]
                        );
                }
            }
        } else {
            $form->setData(
                [
                    'alias'      => $club->getAlias(),
                    'founded'    => $club->getFounded(),
                    'name'       => $club->getName(),
                    'tableName'  => $club->getTableName(),
                    'parentClub' => $club->getParentClub() ? $club->getParentClub()->getId() : null,
                    'logo'       => $club->getLogo(),
                ]
            );
        }
        
        return [
            'form' => $form,
            'club' => $club,
        ];
    }

    public function playersAction()
    {
        $clubsRepository = $this->getObjectManager()->getRepository('Soccer\Entity\Club');

        if (!($this->params('id') && ($club = $clubsRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return [
            'club'    => $club,
            'players' => $this->getObjectManager()
                ->getRepository(ClubPlayer::class)
                ->findBy(['club' => $club], ['position' => 'asc', 'number' => 'asc'])
        ];
    }

    public function deleteAction()
    {
        $clubsRepository = $this->getObjectManager()->getRepository('Soccer\Entity\Club');

        if (!($this->params('id') && ($club = $clubsRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($club);

            $this->flashMessenger()->addInfoMessage(
                $this->translate("Club was deleted")
            );

            return $this->redirect()->toRoute(
                null,
                [
                    'locale' => $this->locale()->current(),
                ]
            );
        }

        return [
            'club' => $club,
        ];
    }

    public function changeLogoAction()
    {
        $clubsRepository = $this->getObjectManager()->getRepository('Soccer\Entity\Club');

        if (!$this->getRequest()->getQuery('return') || !($this->params('id') && ($club = $clubsRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->apiCall()->isReturned()) {
            $result = $this->apiCall()->getResult();
            $logo   = $result['thumbnails']['soccer_club_logo'][0]['href'];
            $small  = $result['thumbnails']['soccer_club_logo_small'][0]['href'];

            $club->setLogo($logo)
                ->setSmallLogo($small);

            return $this->redirect()->toUrl(
                $this->getRequest()->getQuery('return')
            );
        }

        if ($this->apiCall()->isCancelled()) {
            return $this->redirect()->toUrl(
                $this->getRequest()->getQuery('return')
            );
        }

        return $this->apiCall()->call(
            $this->url()->fromRoute(
                'app/admin/images-manager',
                ['locale' => $this->locale()->current()]
            ),
            [
                'mode'       => ManagerController::LISTER_MODE_SELECT_IMAGE,
                'thumbnails' => ['soccer_club_logo', 'soccer_club_logo_small'],
            ]
        );
    }
}