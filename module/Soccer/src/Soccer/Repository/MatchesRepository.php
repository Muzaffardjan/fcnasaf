<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Soccer\Entity\Club;
use Soccer\Entity\Match;

/**
 * MatchesRepository
 */
class MatchesRepository extends EntityRepository
{
    const ORDER_BY_ASC   = 'ASC';
    const ORDER_BY_DESC  = 'DESC';
    const GROUP_BY_DATE  = 'date';
    const GROUP_BY_MONTH = 'month';
    const GROUP_BY_YEAR  = 'year';
    
    /**
     * @return int
     */
    public function count()
    {
        return $this->createQueryBuilder('match')
            ->select('COUNT(match.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param array|null $sortBy
     * @return DoctrinePaginator
     */
    public function findAllPaginated(array $sortBy = null)
    {
        $query = $this->createQueryBuilder('match');

        if (null === $sortBy) {
            $query->addOrderBy("match.date", "DESC");
        } else {
            foreach ($sortBy as $lh => $rh) {
                $query->addOrderBy('match.'.$lh, $rh);
            }
        }

        $query->addOrderBy("match.id", "DESC");

        return new DoctrinePaginator(
            new Paginator(
                $query
            )
        );
    }

    /**
     * @param array      $criteria
     * @param array|null $sortBy
     * @return DoctrinePaginator
     */
    public function findByPaginated(array $criteria = null, array $sortBy = null)
    {
        $query = $this->createQueryBuilder('match');

        if ($criteria) {
            $query->leftJoin('match.season', 'season')
                ->leftJoin('season.tournament', 'tournament')
                ->leftJoin('match.stage', 'subStage')
                ->leftJoin('subStage.parent', 'stage')
                ->leftJoin('match.tour', 'tour')
                ->leftJoin('match.series', 'series');

            $append = ['tournament', 'season', 'stage', 'subStage', 'tour', 'series'];

            foreach ($criteria as $ls => $rs) {
                if (!$rs) {
                    continue;
                }

                if (in_array($ls, $append)) {
                    $ls .= '.id';
                } else {
                    $ls  = 'match.'.$ls;
                }

                $query->andWhere(
                    $query->expr()->eq($ls, is_object($rs)? $rs->getId(): $rs)
                );
            }
        }

        //var_dump($query->__toString());exit;

        if (null === $sortBy) {
            $query->addOrderBy("match.date", "DESC");
        } else {
            foreach ($sortBy as $lh => $rh) {
                $query->addOrderBy('match.'.$lh, $rh);
            }
        }

        $query->addOrderBy($query->expr()->desc('match.id'));

        return new DoctrinePaginator(
            new Paginator(
                $query
            )
        );
    }

    /**
     * @param \DateTime|null $date
     * @param string         $groupBy
     * @param string         $orderBy
     * @param int            $limit
     * @return array
     */
    public function findNextClosest(\DateTime $date = null, $groupBy = null, $orderBy = self::ORDER_BY_ASC, $limit = 10)
    {
        if (null === $date) {
            $date = new \DateTime();
        }

        $qb = $this->createQueryBuilder('match');

        $qb->andWhere($qb->expr()->gte('match.date', ':date'))
            ->setParameter(':date', $date, Type::DATETIME)
            ->setMaxResults($limit);

        if (self::ORDER_BY_DESC == $orderBy) {
            $qb->addOrderBy($qb->expr()->desc('match.date'));
            $qb->addOrderBy($qb->expr()->desc('match.id'));
        } else {
            $qb->addOrderBy($qb->expr()->asc('match.date'));
            $qb->addOrderBy($qb->expr()->asc('match.id'));
        }

        $result = $qb->getQuery()->getResult();

        if (null !== $groupBy) {
            $groupedResult = [];

            /**
             * Forgive me optimization at the time doctrine do not have enough
             * resources to do the things I am going to do below
             *
             * @todo move logic to db
             *
             * @var Match $match
             */
            foreach ($result as $match) {
                $gName = null;

                switch ($groupBy) {
                    case self::GROUP_BY_YEAR:
                        $gName = $match->getDate()->format('Y');
                        break;
                    case self::GROUP_BY_MONTH:
                        $gName = $match->getDate()->format('Y-m');
                        break;
                    case self::GROUP_BY_DATE:
                        $gName = $match->getDate()->format('Y-m-d');
                        break;
                }

                $groupedResult[$gName][] = $match;
            }

            return $groupedResult;
        }

        return $result;
    }

    /**
     * @param Club|int       $club
     * @param \DateTime|null $date
     * @param string         $groupBy
     * @param string         $orderBy
     * @param int            $limit
     * @return array
     */
    public function findNextClosestOfClub($club, \DateTime $date = null, $groupBy = null, $orderBy = self::ORDER_BY_ASC, $limit = 10)
    {
        if ($club instanceof Club) {
            $club = $club->getId();
        }

        if (null === $date) {
            $date = new \DateTime();
        }

        $qb = $this->createQueryBuilder('match');

        $qb
            ->leftJoin('match.host', 'host')
            ->leftJoin('match.guest', 'guest')
            ->andWhere($qb->expr()->eq('host.id', $club))
            ->orWhere($qb->expr()->eq('guest.id', $club))
            ->andWhere($qb->expr()->gte('match.date', ':date'))
            ->setParameter(':date', $date, Type::DATETIME)
            ->setMaxResults($limit);

        if (self::ORDER_BY_DESC == $orderBy) {
            $qb->addOrderBy($qb->expr()->desc('match.date'));
            $qb->addOrderBy($qb->expr()->desc('match.id'));
        } else {
            $qb->addOrderBy($qb->expr()->asc('match.date'));
            $qb->addOrderBy($qb->expr()->asc('match.id'));
        }

        $result = $qb->getQuery()->getResult();

        if (null !== $groupBy) {
            $groupedResult = [];

            /**
             * Forgive me optimization at the time doctrine do not have enough
             * resources to do the things I am going to do below
             *
             * @todo move logic to db
             *
             * @var Match $match
             */
            foreach ($result as $match) {
                $gName = null;

                switch ($groupBy) {
                    case self::GROUP_BY_YEAR:
                        $gName = $match->getDate()->format('Y');
                        break;
                    case self::GROUP_BY_MONTH:
                        $gName = $match->getDate()->format('Y-m');
                        break;
                    case self::GROUP_BY_DATE:
                        $gName = $match->getDate()->format('Y-m-d');
                        break;
                }

                $groupedResult[$gName][] = $match;
            }

            return $groupedResult;
        }

        return $result;
    }
}