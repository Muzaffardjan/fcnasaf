<?php  
namespace Users\Repository;

use Doctrine\ORM\EntityRepository;
use Users\Authentication\Adapter\DoctrineORMAdapterRepositoryInterface;

class UserRepository 
    extends EntityRepository
    implements DoctrineORMAdapterRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findByIdentity($identity)
    {
        // get query builder
        $builder = $this->_em->createQueryBuilder();
        $result  = $builder->select('u')
        ->from('Users\Entity\User', 'u')
        ->where(
            $builder->expr()->eq('u.username', ':username')
        )
        ->setParameter('username', $identity)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();

        if ($result) {
            return current($result);
        }

        return null;
    }
}
