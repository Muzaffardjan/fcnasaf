<?php 
/**
 * Category repository
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\Form\ElementInterface;

class CategoriesRepository 
    extends EntityRepository
{
    /**
     * Gets all categories sorted desc
     */
    public function findAllDesc()
    {
        $builder = $this->_em->createQueryBuilder();
        
        return $builder->select(['c'])
        ->from('Articles\Entity\Category', 'c')
        ->orderBy('c.id', 'DESC')
        ->getQuery()
        ->getResult();
    }

    /**
     * Returns unique uri
     *
     * @param  string  $uri
     * @param  string  $locale
     * @return string
     */
    public function getUniqueUri(
        $uri, 
        $locale = null, 
        $prefix = true, 
        $delimiter = '_'
    ) {
        $builder = $this->_em->createQueryBuilder();

        $builder->select(['c'])
        ->from('Articles\Entity\Category', 'c')
        ->andWhere('c.uri = :uri')
        ->orderBy('c.id', 'DESC')
        ->setParameter(':uri', $uri);

        if (null !== $locale) {
            $builder->andWhere('c.locale = :locale')
            ->setParameter(':locale', $locale);
        }

        $result = $builder->setMaxResults(1)->getQuery()->getResult();

        if ($result) {
            $category = array_pop($result);

            if ($prefix) {
                return sprintf('%s%s%s', $category->id, $delimiter, $uri);
            }

            return sprintf('%s%s%s', $uri, $delimiter, $category->id);
        }

        return $uri;
    }

    /**
     * Returns categories by locale for objectSelect
     *
     * @param   string $locale
     * @return  array
     */
    public function getByLocale($locale) 
    {
        $builder = $this->_em->createQueryBuilder();

        return $builder->select(['c'])
        ->from('Articles\Entity\Category', 'c')
        ->where('c.locale = :locale')
        ->orderBy('c.id', 'DESC')
        ->setParameter('locale', $locale)
        ->getQuery()
        ->getResult();
    }
}