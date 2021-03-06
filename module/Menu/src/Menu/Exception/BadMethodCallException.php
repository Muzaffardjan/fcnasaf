<?php 
/**
 * BadMethodCallException
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Exception;

use \Exception;

class BadMethodCallException extends Exception implements ExceptionInterface 
{
    public $message = 'Bad method call';
}