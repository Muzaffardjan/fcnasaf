<?php 
/**
 * VideosRepository
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Media\Exception;

class VideosRepository extends EntityRepository
{
    /**
     * Returns paginator
     *
     * @param   array       $criteria
     * @return  Paginator
     */
    public function getPaginated(array $criteria = [])
    {
        $query = $this->createQueryBuilder('video')
        ->addOrderBy('video.date', 'DESC')
        ->addOrderBy('video.id', 'DESC');

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('video.'.$key.'=:'.$key)
                ->setParameter(':'.$key, $term);
            }
        }

        return new DoctrineAdapter(
            new Paginator(
                $query
            )
        );
    }
}