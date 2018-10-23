<?php
/**
 * StaffGroupManageController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Menu\Entity\Container;
use Menu\Entity\Page;
use Soccer\Entity\StaffGroup;
use Soccer\Form\StaffGroupForm;
use Soccer\Form\StaffGroupMenuForm;

class StaffGroupManageController extends AbstractObjectManagerAwareController
{
    #region Controller base
    /**
     * @var StaffGroupForm
     */
    protected $staffGroupForm;

    /**
     * @var StaffGroupMenuForm;
     */
    protected $staffGroupMenuForm;

    /**
     * StaffGroupManageController constructor.
     * @param StaffGroupForm $staffGroupForm
     * @param StaffGroupMenuForm $staffGroupMenuForm
     */
    public function __construct(StaffGroupForm $staffGroupForm, StaffGroupMenuForm $staffGroupMenuForm)
    {
        $this->staffGroupForm = $staffGroupForm;
        $this->staffGroupMenuForm = $staffGroupMenuForm;
    }

    /**
     * @return StaffGroupForm
     */
    public function getStaffGroupForm()
    {
        return $this->staffGroupForm;
    }

    /**
     * @param StaffGroupForm $staffGroupForm
     * @return StaffGroupManageController
     */
    public function setStaffGroupForm(StaffGroupForm $staffGroupForm)
    {
        $this->staffGroupForm = $staffGroupForm;
        return $this;
    }

    /**
     * @return StaffGroupForm
     */
    public function getStaffGroupMenuForm()
    {
        return $this->staffGroupMenuForm;
    }

    /**
     * @param mixed $staffGroupMenuForm
     * @return StaffGroupManageController
     */
    public function setStaffGroupMenuForm($staffGroupMenuForm)
    {
        $this->staffGroupMenuForm = $staffGroupMenuForm;
        return $this;
    }
    #endregion

    #region Actions
    public function indexAction()
    {
        $groupsRepository = $this->getObjectManager()
        ->getRepository(StaffGroup::class);

        $groups = $groupsRepository->findBy(
            [],
            [
                'name' => 'ASC',
            ]
        );

        return [
            'groups' => $groups,
        ];
    }

    public function addAction()
    {
        $form    = $this->getStaffGroupForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $group = new StaffGroup();
                $data  = $form->getData();

                $group->setName($data['name']);

                $this->getObjectManager()->persist($group);

                $this->flashMessenger()
                ->addSuccessMessage(
                    $this->translate("New staff group created.")
                );

                return $this->redirect()
                ->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );
            }
        }

        return [
            'form' => $form
        ];
    }

    public function editAction()
    {
        $groupsRepository = $this->getObjectManager()
        ->getRepository(StaffGroup::class);

        if ($this->params('id') && $group = $groupsRepository->find($this->params('id'))) {
            $form    = $this->getStaffGroupForm();
            $request = $this->getRequest();

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();

                    $group->setName($data['name']);

                    $this->flashMessenger()
                        ->addInfoMessage(
                            $this->translate('Staff group was changed.')
                        );

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
                        'name' => $group->getName(),
                    ]
                );
            }

            return [
                'form'  => $form,
                'group' => $group,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteAction()
    {
        $groupsRepository = $this->getObjectManager()
            ->getRepository(StaffGroup::class);

        if ($this->params('id') && $group = $groupsRepository->find($this->params('id'))) {
            $request = $this->getRequest();

            if ($request->getQuery('confirm')) {
                $this->getObjectManager()
                    ->remove($group);

                $this->flashMessenger()
                    ->addWarningMessage(
                        $this->translate("Staff group was deleted.")
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
                'group' => $group,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    /**
     * Menu api
     *
     * @return array|void
     */
    public function menuAction()
    {
        $containerRespository = $this->getObjectManager()
            ->getRepository(Container::class);

        if  (!($this->params('id') && ($container = $containerRespository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->getStaffGroupMenuForm();
        
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $page = new Page();
                /**
                 * @var StaffGroup
                 */
                $group = $form->getData()['group'];

                $page->setLabel($group->getName())
                    ->setRoute('app/staff/group')
                    ->setParams(['action' => 'view', 'id' => $group->getId()])
                    ->setInfo(sprintf("Staff group(%s)", $group->getName()))
                    ->setActive(true)
                    ->setVisible(true);

                $this->getObjectManager()->persist($page);
                $container->getPages()->add($page);
                $this->getObjectManager()->flush();

                return $this->redirect()
                    ->toRoute(
                        'app/admin/menu/page',
                        ['action' => 'edit', 'id' => $page->getId()],
                        [
                            'query' => [
                                'return' => $this->url()->fromRoute(
                                    'app/admin/menu/container',
                                    [
                                        'action' => 'edit',
                                        'id'     => $container->getId(),
                                    ],
                                    true
                                ),
                            ],
                        ],
                        true
                    );
            }
        }

        return [
            'form'      => $form,
            'container' => $container,
        ];
    }
    #endregion
}