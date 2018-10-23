<?php 
/**
 * VideosInfoRepository
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
use Media\Entity\VideoInfo;

class VideosInfoRepository extends EntityRepository
{
    public function findDesc($criteria = null, $limit = null)
    {
        $query = $this->createQueryBuilder('info');

        $query->leftJoin(
            'info.video',
            'video'
        )
        ->addOrderBy('video.date', 'DESC')
        ->addOrderBy('video.id', 'DESC');

        if (null !== $criteria) {
            if (is_array($criteria)) {
                foreach ($criteria as $key => $term) {
                    $query->andWhere('info.'.$key.'=:'.$key)
                    ->setParameter(':'.$key, $term);
                }
            } elseif ($criteria instanceof Criteria) {
                /**
                 * @todo Select by criteria and return pagination
                 */
            } else {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        "'array' or '%s' expected, '%s' given.",
                        Criteria::class,
                        gettype($criteria) == 'object' ?
                        get_class($criteria) :
                        gettype($criteria)
                    )
                );
            }
        }  

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Returns paginator
     *
     * @param   array|Criteria       $criteria
     * @return  Paginator
     */
    public function getPaginated($criteria = null)
    {
        $query = $this->createQueryBuilder('info');

        $query->leftJoin(
            'info.video',
            'video'
        )
        ->addOrderBy('video.date', 'DESC')
        ->addOrderBy('video.id', 'DESC');

        if (null !== $criteria) {
            if (is_array($criteria)) {
                foreach ($criteria as $key => $term) {
                    $query->andWhere('info.'.$key.'=:'.$key)
                    ->setParameter(':'.$key, $term);
                }
            } elseif ($criteria instanceof Criteria) {
                /**
                 * @todo Select by criteria and return pagination
                 */
            } else {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        "'array' or '%s' expected, '%s' given.",
                        Criteria::class,
                        gettype($criteria) == 'object' ?
                        get_class($criteria) :
                        gettype($criteria)
                    )
                );
            }
        }

        return new DoctrineAdapter(
            new Paginator(
                $query
            )
        );
    }

    /**
     * Returns unique uri
     *
     * @param  string  $uri
     * @param  string  $locale
     * @param  Article $current
     * @return string
     */
    public function getUniqueUri(
        $uri, 
        $locale = null, 
        $prefix = true, 
        $delimiter = '_',
        VideoInfo $current = null
    ) {
        $builder = $this->createQueryBuilder('info');

        $builder
        ->where('info.uri = :uri')
        ->orderBy('info.id', 'DESC')
        ->setParameter('uri', $uri);

        if (null !== $locale) {
            $builder->andWhere('info.locale = :locale')
            ->setParameter('locale', $locale);
        }

        if (null !== $current) {
            $builder->andWhere(
                $builder->expr()->neq('info.id', ':id')
            )
            ->setParameter(':id', $current->getId());
        }

        $result = $builder->setMaxResults(1)->getQuery()->getResult();

        if ($result) {
            $info = array_pop($result);

            if ($prefix) {
                return sprintf('%s%s%s', $info->getId(), $delimiter, $uri);
            }

            return sprintf('%s%s%s', $uri, $delimiter, $info->getId());
        }

        return $uri;
    }
}