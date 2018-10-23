<?php 
/**
 * PhotoGalleriesRepository
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;

class PhotoGalleriesRepository extends EntityRepository
{
    /**
     * @param array $criteria
     * @param bool $removeUnfinished
     * @return DoctrinePaginator
     */
    public function getPaginated(array $criteria = [], $removeUnfinished = false)
    {
        $query = $this->createQueryBuilder('gallery')
            ->addOrderBy('gallery.status', 'ASC')
            ->addOrderBy('gallery.createdDate', 'DESC')
            ->addOrderBy('gallery.id', 'DESC');

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('gallery.'.$key.'=:'.$key)->setParameter(':'.$key, $term);
            }
        }

        return new DoctrinePaginator(new Paginator($query));
    }
}