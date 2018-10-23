<?php  
namespace Users\Authentication\Adapter;

interface DoctrineORMAdapterRepositoryInterface
{
    /**
     * Finds user by provided identity
     * 
     * @param string $identity
     * @return null|DoctrineORMAdapterEntityInterface
     */
    public function findByIdentity($identity);
}
