<?php  
namespace Users\Authentication\Adapter;

interface DoctrineORMAdapterEntityInterface
{
    /**
     * Gets current identity's password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Gets crypt object instance for verify password
     *
     * @return Zend\Crypt\Password\PasswordInterface
     */
    public function getCrypt();
}
