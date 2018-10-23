<?php 
/**
 * Invalid locale exception
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Listener\Exception;

use Exception;

class InvalidLocaleException extends Exception implements ExceptionInterface
{
    public $message = 'Invalid locale in route';
}