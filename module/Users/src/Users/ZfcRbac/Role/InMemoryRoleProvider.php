<?php 
/**
 * Little overwrite of original class
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\ZfcRbac\Role;

use ZfcRbac\Role\RoleProviderInterface;
use Rbac\Role\HierarchicalRole;

class InMemoryRoleProvider implements RoleProviderInterface
{
    /**
     * Role storage
     *
     * @var array
     */
    private $roles = [];

    /**
     * Roles config
     *
     * @var array
     */
    private $rolesConfig = [];

    /**
     * @param array
     */
    public function __construct(array $rolesConfig)
    {
        $this->rolesConfig = $rolesConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(array $roleNames)
    {
        $roles = [];

        foreach ($roleNames as $roleName) {
            $roles[] = $this->getRole($roleName);
        }
        return $roles;
    }

    /**
     * Get role by role name
     *
     * @param $roleName
     * @return RoleInterface
     */
    protected function getRole($roleName)
    {
        if (isset($this->roles[$roleName])) {
            return $this->roles[$roleName];
        }

        // If no config, we create a simple role with no permission
        if (!isset($this->rolesConfig[$roleName])) {
            $role = new Role($roleName);
            $this->roles[$roleName] = $role;
            return $role;
        }

        $roleConfig = $this->rolesConfig[$roleName];

        if (isset($roleConfig['children'])) {
            $role = new HierarchicalRole($roleName);
            $childRoles = (array)$roleConfig['children'];
            foreach ($childRoles as $childRole) {
                $childRole = $this->getRole($childRole);
                $role->addChild($childRole);
            }
        } else {
            $role = new Role($roleName);
        }

        $permissions = isset($roleConfig['permissions']) ? $roleConfig['permissions'] : [];
        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }

        $this->roles[$roleName] = $role;

        return $role;
    }
}