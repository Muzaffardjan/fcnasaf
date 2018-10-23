<?php  
namespace Users\Authentication\Adapter\Exception;

use Zend\Authentication\Adapter\Exception\ExceptionInterface;
use Exception;

class InvalidEntityException extends Exception implements ExceptionInterface
{
    
}
