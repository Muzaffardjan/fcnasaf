<?php 
/**
 * Articles repository
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Articles\Entity\Article;

class ArticlesRepository 
    extends EntityRepository
{
    public function findScheduled()
    {
        $qb = $this->createQueryBuilder('article');

        $qb
            ->andWhere(
                $qb->expr()->eq('article.status', Article::STATUS_SCHEDULED)
            )
            ->addOrderBy('article.published');

        return $qb->getQuery()->getResult();
    }

    /**
     * Gets all categories sorted desc
     *
     * @return array
     */
    public function findAllDesc()
    {
        $builder = $this->_em->createQueryBuilder();
        
        return $builder->select(['a'])
        ->from('Articles\Entity\Article', 'a')
        ->addOrderBy('a.published', 'DESC')
        ->addOrderBy('a.status', 'DESC')
        ->addOrderBy('a.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    /**
     * Gets articles sorted desc
     *
     * @param   int     $count
     * @param   bool    $removePlanned
     * @param   array   $criteria
     * @return  array
     */
    public function findDesc(
        $count = null, 
        $removePlanned = false,
        $removeUncategorized = false, 
        array $criteria = []
    ) {
        $query = $this->createQueryBuilder('article')
        ->addOrderBy('article.status', 'ASC')
        ->addOrderBy('article.published', 'DESC')
        ->addOrderBy('article.id', 'DESC');

        if (null !== $count) {
            $query->setMaxResults($count);
        }

        if ($removePlanned) {
            $query->andWhere('article.published < :time')
            ->setParameter(':time', new \DateTime());

            $query->andWhere(
                $query->expr()->eq('article.status', Article::STATUS_ACTIVE)
            );
        }

        if ($removeUncategorized) {
            $query->andWhere($query->expr()->isNotNull('article.category'));
        }

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('article.'.$key.'=:'.$key)
                ->setParameter(':'.$key, $term);
            }
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Returns paginator
     *
     * @param   array       $criteria
     * @return  Paginator
     */
    public function getPaginated(array $criteria = [], $removePlanned = false, $removeUncategorized = false)
    {
        $query = $this->createQueryBuilder('article');

        $query
            ->addOrderBy('article.status', 'ASC')
            ->addOrderBy('article.published', 'DESC')
            ->addOrderBy('article.id', 'DESC');

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('article.'.$key.'=:'.$key)
                ->setParameter(':'.$key, $term);
            }
        }

        if ($removePlanned) {
            $query->andWhere('article.published < :time')
            ->setParameter(':time', new \DateTime());
        }

        if ($removeUncategorized) {
            $query->andWhere($query->expr()->isNotNull('article.category'));
        }

        return new DoctrineAdapter(
            new Paginator(
                $query
            )
        );
    }

    /**
     * Returns paginator 
     *
     * @return Paginator
     */
    public function getPaginatedSearch(
        $term, 
        $titleOnly = false, 
        array $criteria = [],
        $removePlanned = false,
        $removeUncategorized = false
    ) {
        $query = $this->createQueryBuilder('article')
        ->addOrderBy('article.status', 'DESC');

        $query->andWhere('article.title LIKE :term');

        if (!$titleOnly) {
            $query->orWhere('article.body LIKE :term');
        }

        $query->setParameter(':term', '%'.$term.'%');

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('article.'.$key.'=:'.$key)
                ->setParameter(':'.$key, $term);
            }
        }

        if ($removePlanned) {
            $query->andWhere('article.published < :time')
            ->setParameter(':time', new \DateTime());
        }

        if ($removeUncategorized) {
            $query->andWhere($query->expr()->isNotNull('article.category'));
        }

        return new DoctrineAdapter(
            new Paginator(
                $query
            )
        );
    }

    /**
     * Returns last artice if exists
     *
     * @return null|Article
     */
    public function findLast()
    {
        $builder = $this->_em->createQueryBuilder();
        
        $result = $builder->select(['a'])
        ->from('Articles\Entity\Article', 'a')
        ->orderBy('a.id', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if ($result) {
            return $result[0];
        }

        return null;
    }

    /**
     * Returns articles count
     *
     * @return int 
     */
    public function getCount()
    {
        $query = $this->_em->createQuery('SELECT COUNT(a.id) FROM Articles\Entity\Article a');
        return $query->getSingleScalarResult();
    }

    /**
     * Returns related articles
     *
     * @param   Article     $article
     * @param   int         $max
     * @return  array
     */
    public function getRelated(Article $article, $max = 4)
    {
        $criteria = [
            'locale' => $article->getLocale(),
        ];
        $query = $this->createQueryBuilder('article')
        ->addOrderBy('article.status', 'DESC');

        $search = explode(' ', $article->getTitle());

        foreach ($search as $key => $word) {
            if ($key > $max) {
                break;
            }

            $query->orWhere('article.title LIKE :term'.$key)
            ->orWhere('article.body LIKE :term'.$key)
            ->setParameter(':term'.$key, '%'.$word.'%');
        }

        if ($criteria) {
            foreach ($criteria as $key => $term) {
                $query->andWhere('article.'.$key.'=:'.$key)
                ->setParameter(':'.$key, $term);
            }
        }

        $result = $query->setMaxResults($max)
        ->andWhere('article.published <= :time')
        ->setParameter(':time', new \DateTime())
        ->andWhere('article.id != :id')
        ->andWhere($query->expr()->isNotNull('article.category'))
        ->setParameter(':id', $article->getId())
        ->getQuery()
        ->getResult();

        if (!$result && $article->getCategory()) {
            return $this->createQueryBuilder('article')
            ->addOrderBy('article.id', 'DESC')
            ->andWhere('article.id != :id')
            ->setParameter(':id', $article->getId())
            ->andWhere('article.published <= :time')
            ->setParameter(':time', new \DateTime())
            ->andWhere('article.category = :category')
            ->setParameter(':category', $article->getCategory())
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
        }

        return $result;
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
        Article $current = null
    ) {
        $builder = $this->_em->createQueryBuilder();

        $builder->select(['a'])
        ->from('Articles\Entity\Article', 'a')
        ->where('a.uri = :uri')
        ->orderBy('a.id', 'DESC')
        ->setParameter('uri', $uri);

        if (null !== $locale) {
            $builder->andWhere('a.locale = :locale')
            ->setParameter('locale', $locale);
        }

        if (null !== $current) {
            $builder->andWhere(
                $builder->expr()->neq('a.id', ':id')
            )
            ->setParameter(':id', $current->getId());
        }

        $result = $builder->setMaxResults(1)->getQuery()->getResult();

        if ($result) {
            $article = array_pop($result);

            if ($prefix) {
                return sprintf('%s%s%s', $article->getId(), $delimiter, $uri);
            }

            return sprintf('%s%s%s', $uri, $delimiter, $article->getId());
        }

        return $uri;
    }
}