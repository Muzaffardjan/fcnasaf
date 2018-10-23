<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

/**
 * PlayerPositionsRepository
 */
class PlayerPositionsRepository extends EntityRepository
{
    /**
     * @return DoctrinePaginator
     */
    public function findAllPaginated()
    {
        $qb = $this->createQueryBuilder('pp');

        $qb->addOrderBy($qb->expr()->asc('pp.order'));

        return new DoctrinePaginator(
            new Paginator(
                $qb->getQuery()
            )
        );
    }

    /**
     * @return int
     */
    public function getNextOrder()
    {
        $qb = $this->createQueryBuilder('pp');

        $qb->select($qb->expr()->max('pp.order'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}