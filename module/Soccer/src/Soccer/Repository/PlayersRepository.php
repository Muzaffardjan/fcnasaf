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
use Soccer\Entity\Player;

/**
 * PlayersRepository
 */
class PlayersRepository extends EntityRepository
{
    /**
     * Paginated result
     *
     * @param array|null $sortBy
     * @return DoctrinePaginator
     */
    public function findAllPaginated(array $sortBy = null)
    {
        $query = $this->createQueryBuilder('player');

        if (null !== $sortBy) {
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

    /**
     * Returns players that have image card
     *
     * @param null $clubId
     * @return array
     */
    public function findPlayersWithImageCards($clubId = null)
    {
        $qb = $this->createQueryBuilder('player');

        $qb->innerJoin('player.clubs', 'cPlayer');

        if (null !== $clubId) {
            $qb->andWhere(
                $qb->expr()->eq('cPlayer.club', $clubId)
            );
        }

        $qb
            ->andWhere(
                $qb->expr()->isNotNull('player.photos')
            )
            ->andWhere(
                $qb->expr()->isNotNull('player.settings')
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns players that not playing e.g free players
     *
     * @return array
     */
    public function findNotPlaying()
    {
        $qb = $this->createQueryBuilder('player');

        $qb->orWhere($qb->expr()->isNull('player.status'))
            ->orWhere($qb->expr()->eq('player.status', Player::STATUS_FREE))
            ->addOrderBy($qb->expr()->asc('player.alias'));

        $qb->leftJoin('player.clubs', 'cplayer')
            ->andWhere($qb->expr()->isNull('cplayer.id'));

        return $qb->getQuery()->getResult();
    }
}