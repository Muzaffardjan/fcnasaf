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
use Soccer\Entity\PlayerPosition;
use Soccer\Form\PlayerPositionsForm;
use Zend\Paginator\Paginator;
use Zend\View\Model\JsonModel;

/**
 * PlayerPositionsController
 */
class PlayerPositionsController extends AbstractObjectManagerAwareController
{
    /**
     * @var PlayerPositionsForm
     */
    protected $playerPositionsForm;

    /**
     * PlayerPositionsController constructor.
     *
     * @param PlayerPositionsForm $playerPositionsForm
     */
    public function __construct(PlayerPositionsForm $playerPositionsForm)
    {
        $this->playerPositionsForm = $playerPositionsForm;
    }

    public function indexAction()
    {
        $request         = $this->getRequest();
        $ppRepository    = $this->getObjectManager()->getRepository(PlayerPosition::class);
        $defaultLimit    = 10;
        $positions       = new Paginator(
            $ppRepository->findAllPaginated()
        );

        if ($request->isXmlHttpRequest()) {
            $positions->setItemCountPerPage($request->getQuery('limit', $defaultLimit))
                ->setCurrentPageNumber(($request->getQuery('offset', 0) / $positions->getItemCountPerPage()) + 1);

            $array = [];

            foreach ($positions as $position) {
                $array[] = [
                    'id'    => $position->getId(),
                    'label' => $position->getLabel(),
                    'plural'=> $position->getPluralLabel(),
                    'order' => $position->getOrder(),
                ];
            }

            return new JsonModel(
                [
                    'total' => $positions->getTotalItemCount(),
                    'rows'  => $array,
                ]
            );
        } else {
            $positions->setCurrentPageNumber($request->getQuery('page', 1))
                ->setItemCountPerPage($defaultLimit);
        }

        return [
            'positions' => $positions,
        ];
    }

    public function addAction()
    {
        $request      = $this->getRequest();
        $form         = $this->getPlayerPositionsForm();
        $ppRepository = $this->getObjectManager()->getRepository(PlayerPosition::class);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $playerPosition = new PlayerPosition();
                $data           = $form->getData();

                $playerPosition->setLabel($data['label'])
                    ->setPluralLabel($data['pluralLabel'])
                    ->setOrder($data['order']);

                $this->getObjectManager()->persist($playerPosition);

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
            }
        } else {
            $form->get('order')->setValue($ppRepository->getNextOrder()+1);
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        $ppRepository = $this->getObjectManager()->getRepository(PlayerPosition::class);

        if (!($this->params('id')
            && $playerPosition = $ppRepository->find($this->params('id')))
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request      = $this->getRequest();
        $form         = $this->getPlayerPositionsForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data           = $form->getData();

                $playerPosition->setLabel($data['label'])
                    ->setPluralLabel($data['pluralLabel'])
                    ->setOrder($data['order']);

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
            }
        } else {
            $form->setData(
                [
                    'label'       => $playerPosition->getLabel(),
                    'pluralLabel' => $playerPosition->getPluralLabel(),
                    'order'       => $playerPosition->getOrder(),
                ]
            );
        }

        return [
            'form' => $form,
        ];
    }

    public function deleteAction()
    {
        $ppRepository = $this->getObjectManager()->getRepository(PlayerPosition::class);

        if (!($this->params('id')
            && $playerPosition = $ppRepository->find($this->params('id')))
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($playerPosition);

            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );
        }

        return [
            'position' => $playerPosition,
        ];
    }

    /**
     * @return PlayerPositionsForm
     */
    public function getPlayerPositionsForm()
    {
        return $this->playerPositionsForm;
    }

    /**
     * @param PlayerPositionsForm $playerPositionsForm
     * @return self
     */
    public function setPlayerPositionsForm(PlayerPositionsForm $playerPositionsForm)
    {
        $this->playerPositionsForm = $playerPositionsForm;

        return $this;
    }
}