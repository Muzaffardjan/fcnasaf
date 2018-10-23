<?php 
/**
 * Role
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\ZfcRbac\Role;

use Rbac\Role\Role as ZfcRbacRole;

class Role extends ZfcRbacRole
{
    /**
     * @const string Superuser role name
     */
    const SUPERUSER = 'Superuser';

    /**
     * @const string Guest role name
     */
    const GUEST     = 'Guest';

    /**
     * {@inheritdoc}
     */
    public function hasPermission($permission)
    {
        if ($this->getName() == self::SUPERUSER) {
            return true;
        }

        return isset($this->permissions[(string) $permission]);
    }
}