<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

/**
 * StadiumsRepository
 */
class StadiumsRepository extends EntityRepository
{
    /**
     * @return DoctrinePaginator
     */
    public function findAllPaginated(array $sortBy = null)
    {
        $query = $this->createQueryBuilder('stadium');

        if ($sortBy) {
            foreach ($sortBy as $lh => $rh) {
                $query->addOrderBy('stadium.'.$lh, $rh);
            }
        }

        return new DoctrinePaginator(
            new Paginator(
                $query
            )
        );
    }
}