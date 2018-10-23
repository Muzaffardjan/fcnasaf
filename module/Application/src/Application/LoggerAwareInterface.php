<?php  
namespace Application;

use Zend\Log\Logger;

interface LoggerAwareInterface
{
    public function setLogger(Logger $logger);
}
