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
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Exception\InvalidArgumentException;
use Soccer\Exception\RuntimeException;

/**
 * SeriesRepository
 */
class SeriesRepository extends EntityRepository
{
    /**
     * @param   Stage $stage
     * @return  array
     */
    public function findSeriesOf(Stage $stage)
    {
        $qb = $this->createQueryBuilder('series');

        $qb
            ->join(Stage::class, 'stage')
            ->andWhere(
                $qb->expr()->lt('series.stage', $stage->getId())
            )
            ->andWhere(
                $qb->expr()->eq('stage.parent', $stage->getParent()->getId())
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param   int|Stage $stage
     * @return  array
     */
    public function findFinalSeriesOf($stage)
    {
        if ($stage instanceof Stage) {
            $stage = $stage->getId();
        } elseif (!is_int($stage)) {
            throw new InvalidArgumentException(
                sprintf(
                    '$stage must be integer or instance of %s, %s given.',
                    Stage::class,
                    gettype($stage) == 'object' ?
                        get_class($stage) :
                        gettype($stage)
                )
            );
        }

        $qb = $this->createQueryBuilder('series');

        $qb
            ->join('series.stage', 'subStage')
            ->join('subStage.parent', 'stage')
            ->andWhere(
                $qb->expr()->eq('stage.id', $stage)
            )
            ->andWhere(
                $qb->expr()->eq('series.final', true)
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllSeriesOf(Stage $stage) {
        if ($stage->getType() != Stage::TYPE_PLAY_OFF) {
            throw new RuntimeException(
                "Stage type must be play-off"
            );
        }

        $qb = $this->createQueryBuilder('series');

        $qb->join('series.stage', 'subStage')
            ->join('subStage.parent', 'stage')
            ->where('stage.id = :stage_id')
            ->setParameter('stage_id', $stage->getId());

        return $qb->getQuery()->getResult();
    }
}