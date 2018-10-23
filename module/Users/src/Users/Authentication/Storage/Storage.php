<?php 
namespace Users\Authentication\Storage;

use Zend\Authentication\Storage\Session;
use Doctrine\Common\Persistence\ObjectRepository;

use Users\Entity\User;
use Users\Authentication\Adapter\DoctrineORMAdapterEntityInterface;

class Storage extends Session
{
    const STORAGE_NAME = 'ForeachSoft';

    /**
     * @var ObjectRepository
     */
    protected $repository;

    public function __construct(ObjectRepository $retrieveRepository)
    {
        parent::__construct(self::STORAGE_NAME);
        $this->repository = $retrieveRepository;
    }

    /**
     * Gets ORM's retrieve object repository
     *
     * @return ObjectRepository
     */
    public function getRetriveRepository()
    {
        return $this->repository;
    }

    /**
     * {@inheritDoc}
     */
    public function write($identity)
    {
        if(!$identity instanceof DoctrineORMAdapterEntityInterface)
        {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '$identity must be instance of \'%s\', \'%s\' given.',
                    'Users\Authentication\Adapter\DoctrineORMAdapterEntityInterface',
                    gettype($identity) == 'object' ? get_class($identity) : gettype($identity)
                )
            );            
        }

        return parent::write($identity->id);
    }

    /**
     * {@inheritDoc}
     * @return User|bool
     */
    public function read()
    {
        if(parent::isEmpty())
        {
            return false;
        }

        $id = parent::read();

        return $this->getRetriveRepository()->find((int) $id);
    }
}
