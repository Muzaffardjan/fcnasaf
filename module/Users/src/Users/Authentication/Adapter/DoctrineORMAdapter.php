<?php  
namespace Users\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Config\Config;
use Zend\Crypt\Password\PasswordInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Users\Authentication\Adapter\Exception;

class DoctrineORMAdapter 
    implements AdapterInterface
{
    protected $identity;

    protected $password;

    protected $objectManager;

    protected $config;
    
    public function __construct(ObjectManager $oManager, Config $adapterConfig)
    {
        $this->setObjectManager($oManager);
        $this->setConfig($adapterConfig);
    }

    public function setObjectManager(ObjectManager $manager)
    {
        $this->objectManager = $manager;

        return $this;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        if(!$this->identity || !$this->password)
        {
            throw new Exception\RuntimeException(
                "Identity or password not provided!"
            );
        }

        $config = $this->getConfig();

        if(!isset($config['entity']))
        {
            throw new Exception\BadConfigException('Invalid config provided');
        }

        $repository = $this->getObjectManager()
        ->getRepository($config['entity']);

        if($repository instanceof DoctrineORMAdapterRepositoryInterface)
        {
            if(!($identity = $repository->findByIdentity($this->identity)))
            {
                return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $this->identity);
            }

            if(!$identity instanceof DoctrineORMAdapterEntityInterface)
            {
                throw new Exception\InvalidEntityException(
                    sprintf(
                        'Returned entity must be instance of \'%s\'. \'%s\' returned',
                        'Users\Authentication\Adapter\DoctrineORMAdapterEntityInterface',
                        gettype($identity) == 'object' ? get_class($identity) : gettype($identity)
                    )
                );
            }

            if(!$identity->getCrypt() instanceof PasswordInterface)
            {
                throw new \Zend\Authentication\Adapter\Exception\UnexpectedValueException('Provided crypt must be instance of \'Zend\Crypt\Password\PasswordInterface\'');
            }

            if(!$identity->getCrypt()->verify($this->password, $identity->getPassword()))
            {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity);
            }

            return new Result(Result::SUCCESS, $identity);
        }

        throw new Exception\InvalidRepositoryException(
            sprintf(
                'Invalid repository provided. Repository must be instance of \'%s\'',
                'Users\Authentication\Adapter\DoctrineORMAdapterEntityInterface'
            )
        );
    }
}
