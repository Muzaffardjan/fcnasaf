<?php 
/**
 * User entity
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @license Foreach.Soft 2016
 * @version 1.0.0
 */
namespace Users\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Crypt\Password\Bcrypt;
use ZfcRbac\Identity\IdentityInterface;

use Users\Authentication\Adapter\DoctrineORMAdapterEntityInterface;
use Application\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Users\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User 
    extends AbstractEntity 
    implements DoctrineORMAdapterEntityInterface,
               IdentityInterface
{
    const BCRYPT_COST = 12;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true, length=32)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=24)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * String that contains user roles separated by comma 
     *
     * @var string $roles
     * @ORM\Column(type="simple_array")
     */
    protected $roles;

    /**
     * Hashing adapter holder
     *
     * @var Bcrypt $bcrypt
     */
    protected $bcrypt;

    /**
     * {@inheritDoc}
     */
    protected $__readonly = ['id', 'bcrypt'];

    /**
     * {@inheritDoc}
     */
    public function getCrypt()
    {
        if(null === $this->bcrypt)
        {
            $this->bcrypt = new Bcrypt(
                array(
                    'cost' => self::BCRYPT_COST,
                )
            );
        }

        return $this->bcrypt;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets user password with hashing.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        // Hash password
        $this->password = $this->getCrypt()->create($password);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
