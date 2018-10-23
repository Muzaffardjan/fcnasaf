<?php  
namespace Users\Authentication\Adapter\Exception;

use Zend\Authentication\Adapter\Exception\ExceptionInterface;
use Exception;

class InvalidRepositoryException extends Exception implements ExceptionInterface
{
    
}
