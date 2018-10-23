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
use Soccer\Entity\Tournament;
use Soccer\Form\TournamentsForm;

/**
 * TournamentManageController
 */
class TournamentManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var TournamentsForm
     */
    protected $tournamentsForm;

    /**
     * TournamentManageController constructor.
     *
     * @param TournamentsForm $tournamentsForm
     */
    public function __construct(TournamentsForm $tournamentsForm)
    {
        $this->tournamentsForm = $tournamentsForm;
    }

    public function indexAction()
    {
        return [
            'tournaments' => $this->getObjectManager()
                ->getRepository(Tournament::class)
                ->findAll()
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->tournamentsForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $tournament = new Tournament();
                $data       = $form->getData();

                $tournament->setAliasName($data['aliasName'])
                    ->setLabel($data['label']);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('New tournament added')
                    );

                $this->getObjectManager()->persist($tournament);

                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
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
        $tournamentRepository = $this->getObjectManager()->getRepository(Tournament::class);

        if ($this->params('id')) {
            /**
             * @var Tournament $tournament
             */
            $tournament = $tournamentRepository->find($this->params('id'));

            if (!$tournament instanceof Tournament) {
                $this->getResponse()->setStatusCode(404);
                return;
            }
        }

        $form = $this->tournamentsForm;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $tournament->setAliasName($data['aliasName'])
                    ->setLabel($data['label']);

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate('Changes saved')
                    );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current()
                        ]
                    );
            }
        } else {
            $form->setData(
                [
                    'aliasName' => $tournament->getAliasName(),
                    'label'     => $tournament->getLabel(),
                ]
            );
        }

        return [
            'form'       => $form,
            'tournament' => $tournament,
        ];
    }

    public function deleteAction()
    {
        $tournamentRepository = $this->getObjectManager()->getRepository(Tournament::class);

        if ($this->params('id')) {
            /**
             * @var Tournament $tournament
             */
            $tournament = $tournamentRepository->find($this->params('id'));

            if (!$tournament instanceof Tournament) {
                $this->getResponse()->setStatusCode(404);
                return;
            }
        }
        
        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($tournament);

            $this->flashMessenger()
                ->addWarningMessage(
                    $this->translate('Item was deleted')
                );
            
            return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );
        }
        
        return [
            'tournament' => $tournament,
        ];
    }
}