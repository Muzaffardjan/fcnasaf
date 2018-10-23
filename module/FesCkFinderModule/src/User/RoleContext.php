<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\User;

use CKSource\CKFinder\Acl\User\RoleContextInterface;

/**
 * RoleContext
 */
class RoleContext implements RoleContextInterface
{
    /**
     * @var string
     */
    protected $role;

    /**
     * @inheritDoc
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}