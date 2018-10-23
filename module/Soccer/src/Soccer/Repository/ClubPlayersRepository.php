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
use Soccer\Entity\Club;

/**
 * ClubPlayersRepository
 */
class ClubPlayersRepository extends EntityRepository
{
    /**
     * Returns players of given club grouped by position
     *
     * @param Club $club
     * @return array
     */
    public function findGroupedByPositionPlayersOf(Club $club)
    {
        $qb = $this->createQueryBuilder('player');

        $qb
            ->join('player.club', 'club')
            ->join('player.position', 'position')
            ->andWhere(
                $qb->expr()->eq('club.id', $club->getId())
            )
            ->andWhere(
                $qb->expr()->isNull('player.until')
            )
            ->addOrderBy('position.id')
            ->addOrderBy('position.order')
            ->addOrderBy('player.number');

        return $qb->getQuery()->getResult();
    }

    public function findPlayersOf(Club $club)
    {
        $qb = $this->createQueryBuilder('player');

        $qb
            ->join('player.club', 'club')
            ->join('player.position', 'position')
            ->andWhere(
                $qb->expr()->eq('club.id', $club->getId())
            )
            ->andWhere(
                $qb->expr()->isNull('player.until')
            )
            ->addOrderBy('position.id')
            ->addOrderBy('position.order')
            ->addOrderBy('player.number');

        return $qb->getQuery()->getResult();
    }
}