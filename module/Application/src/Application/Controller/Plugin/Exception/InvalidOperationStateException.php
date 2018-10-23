<?php 
/**
 * InvalidOperationStateException
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Controller\Plugin\Exception;

use \Exception;

class InvalidOperationStateException 
extends Exception 
implements ExceptionInterface
{
    public $message = 'Invalid operation for this state. Diffrent state needed.';
}