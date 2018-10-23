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
 * ClubsRepository
 */
class ClubsRepository extends EntityRepository
{
    public function findAllPaginated(array $sortBy = null)
    {
        $query = $this->createQueryBuilder('club');

        if (null === $sortBy) {
            $query->addOrderBy("club.alias");
        } else {
            foreach ($sortBy as $lh => $rh) {
                $query->addOrderBy($lh, $rh);
            }
        }

        return new DoctrinePaginator(
            new Paginator(
                $query
            )
        );
    }
}