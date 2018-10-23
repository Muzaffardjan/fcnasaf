<?php 
/**
 * InvalidArgumentException
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Exception;

use \Exception;

class InvalidArgumentException 
extends Exception
implements ExceptionInterface 
{
    public $message = 'Invalid argument given.';
}