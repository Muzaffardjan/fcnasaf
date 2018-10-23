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
use Soccer\Entity\Season;
use Soccer\Entity\Stage;

/**
 * StageRepository
 */
class StageRepository extends EntityRepository
{
    public function findAllForTable()
    {
        $qb = $this->createQueryBuilder('stage');

        $qb->leftJoin('stage.season', 'season')
            ->leftJoin('stage.parent', 'parent')
            ->andWhere($qb->expr()->eq('stage.type', '?0'))
            ->orWhere($qb->expr()->eq('stage.type', '?1'))
            ->andWhere($qb->expr()->neq('season.type', '?2'))
            ->andWhere($qb->expr()->eq('season.visible', '?3'))
            ->setParameter(0, Stage::TYPE_SINGLE)
            ->setParameter(1, Stage::TYPE_SUB_STAGE)
            ->setParameter(2, Season::TYPE_EXHIBITION)
            ->setParameter(3, true);

        return $qb->getQuery()->getResult();
    }
}