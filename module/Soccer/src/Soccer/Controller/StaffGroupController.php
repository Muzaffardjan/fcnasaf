<?php
/**
 * StaffGroupController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Controller;

use Application\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\StaffGroup;

class StaffGroupController extends AbstractObjectManagerAwareController
{
    /**
     * StaffGroupController constructor.
     */
    public function __construct()
    {
        $this->setNeedFlush(false);
    }

    /**
     * @return array|void
     */
    public function viewAction()
    {
        $staffGroupRepository = $this->getObjectManager()
            ->getRepository(StaffGroup::class);

        if (!($this->params('id') && ($group = $staffGroupRepository->find($this->params('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return [
            'group' => $group,
        ];
    }
}