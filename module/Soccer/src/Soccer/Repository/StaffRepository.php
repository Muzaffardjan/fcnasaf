<?php
/**
 * StaffRepository
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Soccer\Entity\StaffGroup;

class StaffRepository extends EntityRepository
{
    /**
     * @param array|null $criteria
     * @return DoctrinePaginator
     */
    public function getPaginated(array $criteria = null, array $sortBy = null)
    {
        $query = $this->createQueryBuilder('staff');

        $query->join('staff.group', 'group');

        if (null === $sortBy) {
            $query->addOrderBy("group.name");
        } else {
            foreach ($sortBy as $lh => $rh) {
                $query->addOrderBy($lh, $rh);
            }
        }

        if (null !== $criteria) {
            foreach ($criteria as $lh => $rh) {
                $query->andWhere($query->expr()->eq($lh, ':'.$lh))
                    ->setParameter(':'.$lh, $rh);
            }
        }

        return new DoctrinePaginator(
            new Paginator(
                $query
            )
        );
    }
}