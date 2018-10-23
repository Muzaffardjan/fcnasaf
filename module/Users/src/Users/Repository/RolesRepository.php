<?php 
/**
 * Roles entity repository
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\Repository;

use Doctrine\ORM\EntityRepository;

use Users\Entity\Role;

class RolesRepository extends EntityRepository
{
    /**
     * Gets role by name
     *
     * @throws Exception\InvalidArgumentException
     * @param string $name
     * @return null|Role
     */
    public function findByName($name)
    {
        if (!is_string($name)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    "Role name must be string '%s' given.",
                    gettype($name) == 'object' ?
                        get_class($name) :
                        gettype($name)
                )
            );
        }

        // Get query builder
        $builder = $this->_em->createQueryBuilder();

        // Get all roles with given name
        $role = $builder->select('r')
        ->from('Users\Entity\Role', 'r')
        ->where('r.name = :name')
        ->setParameter('name', $name)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if (is_array($role)) {
            return current($role);
        }

        return $role;
    }

    /**
     * Is conflict for role name exists in table
     * because role name must be unique
     * if role given role role id will be checked too
     *
     * @throws Exception\InvalidArgumentException
     * @param string $name
     * @param null|Role $role
     * @return bool
     */
    public function isConflicting($name, Role $role = null)
    {
        if (!is_string($name)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    "Role name must be string '%s' given.",
                    gettype($name) == 'object' ?
                        get_class($name) :
                        gettype($name)
                )
            );
        }

        // Get query builder
        $builder = $this->_em->createQueryBuilder();

        // Get all roles with given name
        $conflicts = $builder->select('r')
        ->from('Users\Entity\Role', 'r')
        ->where('r.name = :name')
        ->setParameter('name', $name)
        ->getQuery()
        ->getResult();

        if (!$role) {
            return (bool) count($conflicts);
        }

        // Check for role id
        foreach ($conflicts as $conflict) {
            // if conflict id equals to given role id
            // than it conflicts
            if ($conflict->getId() !== $role->getId()) {
                return true;
            }
        }

        return false;
    }
}