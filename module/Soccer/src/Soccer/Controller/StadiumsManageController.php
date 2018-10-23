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
use Soccer\Entity\Stadium;
use Soccer\Form\StadiumsForm;
use Zend\Paginator\Paginator;
use Soccer\Repository\StadiumsRepository;
use Zend\View\Model\JsonModel;

/**
 * StadiumsManageController
 */
class StadiumsManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var StadiumsForm
     */
    protected $stadiumsForm;

    /**
     * StadiumsManageController constructor.
     *
     * @param StadiumsForm $stadiumsForm
     */
    public function __construct(StadiumsForm $stadiumsForm)
    {
        $this->stadiumsForm = $stadiumsForm;
    }

    public function indexAction()
    {
        /**
         * @var StadiumsRepository $stadiumsR
         */
        $request         = $this->getRequest();
        $stadiumsR       = $this->getObjectManager()->getRepository(Stadium::class);
        $defaultLimit    = 10;
        $stadiums        = new Paginator(
            $stadiumsR->findAllPaginated(
                [
                    $request->getQuery('sort') ? $request->getQuery('sort') : 'id' => strtoupper($request->getQuery('order', 'asc'))
                ]
            )
        );

        if ($request->isXmlHttpRequest()) {
            $stadiums->setItemCountPerPage($request->getQuery('limit', $defaultLimit))
                ->setCurrentPageNumber(($request->getQuery('offset', 0) / $stadiums->getItemCountPerPage()) + 1);

            $array = [];

            foreach ($stadiums as $stadium) {
                $array[] = [
                    'id'            => $stadium->getId(),
                    'name'          => $stadium->getName(),
                    'located'       => $stadium->getLocated(),
                    'owner'    => [
                        'logo' => $stadium->getOwner()? $stadium->getOwner()->getSmallLogo(): null,
                        'name' => $stadium->getOwner()? $stadium->getOwner()->getName(): null,
                    ],
                ];
            }

            return new JsonModel(
                [
                    'total' => $stadiums->getTotalItemCount(),
                    'rows'  => $array,
                ]
            );
        } else {
            $stadiums->setCurrentPageNumber($request->getQuery('page', 1))
                ->setItemCountPerPage($defaultLimit);
        }

        return [
            'stadiums' => $stadiums,
        ];
    }

    public function addAction()
    {
        $form    = $this->stadiumsForm;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data    = $form->getData();
                $stadium = new Stadium();

                $stadium->setName($data['name'])
                    ->setLocated($data['located']);

                if ($data['owner'] instanceof Club) {
                    $stadium->setOwner($data['owner']);
                }

                $this->getObjectManager()->persist($stadium);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate(
                            "New stadium was added"
                        )
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
         * @var Stadium $stadium
         */
        $form      = $this->stadiumsForm;
        $request   = $this->getRequest();
        $stadiumsR = $this->getObjectManager()
            ->getRepository(Stadium::class);
        $stadium   = $this->params('id');

        if (!($stadium && $stadium = $stadiumsR->find($stadium))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $stadium->setName($data['name'])
                    ->setLocated($data['located']);

                if ($data['owner'] instanceof Club) {
                    $stadium->setOwner($data['owner']);
                }

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate(
                            "Changes saved"
                        )
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
                    'name'    => $stadium->getName(),
                    'located' => $stadium->getLocated(),
                    'owner'   => $stadium->getOwner(),
                ]
            );
        }

        return [
            'stadium' => $stadium,
            'form'    => $form,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Stadium $stadium
         */
        $request   = $this->getRequest();
        $stadiumsR = $this->getObjectManager()
            ->getRepository(Stadium::class);
        $stadium   = $this->params('id');

        if (!($stadium && $stadium = $stadiumsR->find($stadium))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->getQuery('confirm')) {
            $this->getObjectManager()->remove($stadium);

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
            'stadium' => $stadium,
        ];
    }
}