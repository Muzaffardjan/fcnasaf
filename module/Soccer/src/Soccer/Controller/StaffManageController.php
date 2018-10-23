<?php
/**
 * StaffManageController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Staff;
use Soccer\Form\StaffForm;
use Zend\Paginator\Paginator;
use Zend\Stdlib\ArrayUtils;
use Zend\Uri\UriFactory;
use Zend\View\Model\JsonModel;

class StaffManageController extends AbstractObjectManagerAwareController
{
    #region Controller base
    /**
     * @var StaffForm
     */
    protected $staffForm;

    /**
     * StaffManageController constructor.
     * @param StaffForm $staffForm
     */
    public function __construct(StaffForm $staffForm)
    {
        $this->staffForm = $staffForm;
    }

    /**
     * @return StaffForm
     */
    public function getStaffForm()
    {
        return $this->staffForm;
    }

    /**
     * @param StaffForm $staffForm
     * @return StaffManageController
     */
    public function setStaffForm(StaffForm $staffForm)
    {
        $this->staffForm = $staffForm;
        return $this;
    }
    #endregion

    #region Actions
    public function indexAction()
    {
        $defaultLimit = 10;
        $staffRepository = $this->getObjectManager()
            ->getRepository(Staff::class);
        $request = $this->getRequest();
        $staff   = new Paginator(
            $staffRepository->getPaginated(
                null,
                [
                    (($request->getQuery('sort') && $request->getQuery('sort') != 'group') ? 'staff.'.$request->getQuery('sort') : 'group.name') => strtoupper($request->getQuery('order', 'asc'))
                ]
            )
        );

        if ($request->isXmlHttpRequest()) {
            $staff->setCurrentPageNumber($request->getQuery('offset', 0) + 1)
                ->setItemCountPerPage($request->getQuery('limit', $defaultLimit));

            $array = [];

            foreach ($staff as $member) {
                $array[] = [
                    'id'            => $member->getId(),
                    'photo'         => $member->getPhoto(),
                    'firstname'     => $member->getFirstname(),
                    'lastname'      => $member->getLastname(),
                    'birthDate'     => $member->getBirthDate()->format('d.m.Y'),
                    'inClubSince'   => $member->getInClubSince(),
                    'position'      => $member->getPosition(),
                    'group'         => $member->getGroup()->getName(),
                ];
            }

            return new JsonModel(
                [
                    'total' => $staff->getTotalItemCount(),
                    'rows'  => $array,
                ]
            );
        } else {
            $staff->setCurrentPageNumber($request->getQuery('page', 1))
                ->setItemCountPerPage($defaultLimit);
        }

        return [
            'staff' => $staff,
        ];
    }

    public function photoAction()
    {
        if (!$this->getRequest()->getQuery('return')) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if (!$this->apiCall()->isReturned() && !$this->apiCall()->isCancelled()) {
            return $this->apiCall()->call(
                $this->url()->fromRoute(
                    'app/admin/images-manager',
                    [
                        'locale' => $this->locale()->current(),
                    ]
                ),
                [
                    'mode'       => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGE,
                    'thumbnails' => ['soccer_staff_photo'],
                ]
            );
        }

        if ($this->apiCall()->isReturned()) {
            $return = UriFactory::factory($this->getRequest()->getQuery('return'));

            $return->setQuery(
                ['image' => $this->apiCall()->getResult()['thumbnails']['soccer_staff_photo'][0]['href']]
            );

            return $this->redirect()
                ->toUrl($return);
        }
    }

    public function addAction()
    {
        $form = $this->getStaffForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $staff = new Staff();
                $data  = $form->getData();

                $staff->setPhoto($data['photo'])
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setBirthDate(\DateTime::createFromFormat('d.m.Y', $data['birthDate']))
                    ->setInClubSince($data['inClubSince'])
                    ->setPosition($data['position'])
                    ->setGroup($data['group']);

                $this->getObjectManager()->persist($staff);
                $data['group']->getMembers()->add($staff);

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate("New staff member added.")
                    );

                return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
            }
        } elseif ($request->getQuery('image')) {
            $form->get('photo')->setValue($request->getQuery('image'));
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        $staffRepository = $this->getObjectManager()->getRepository(Staff::class);

        if (!$this->params('id') || !($staff = $staffRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();
        $form    = $this->getStaffForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $staff->setPhoto($data['photo'])
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setBirthDate(\DateTime::createFromFormat('d.m.Y', $data['birthDate']))
                    ->setInClubSince($data['inClubSince'])
                    ->setPosition($data['position'])
                    ->setGroup($data['group']);

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate("Staff member info changed.")
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
                    'photo'         => $staff->getPhoto(),
                    'firstname'     => $staff->getFirstname(),
                    'lastname'      => $staff->getLastname(),
                    'birthDate'     => $staff->getBirthDate()->format('d.m.Y'),
                    'inClubSince'   => $staff->getInClubSince(),
                    'position'      => $staff->getPosition(),
                    'group'         => $staff->getGroup()->getId(),
                ]
            );
        }

        if ($request->getQuery('image')) {
            $form->get('photo')->setValue($request->getQuery('image'));
        }

        return [
            'form'  => $form,
            'staff' => $staff,
        ];
    }

    public function deleteAction()
    {
        $staffRepository = $this->getObjectManager()->getRepository(Staff::class);

        if (!$this->params('id') || !($staff = $staffRepository->find($this->params('id')))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $request = $this->getRequest();

        if ($request->getQuery('confirm')) {
            $this->getObjectManager()->remove($staff);

            $this->flashMessenger()
                ->addWarningMessage(
                    $this->translate("Staff member was deleted")
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
            'staff' => $staff,
        ];
    }
    #endregion
}