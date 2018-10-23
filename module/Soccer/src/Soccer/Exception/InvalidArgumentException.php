<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Exception;

/**
 * InvalidArgumentException
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
    public $message = 'Invalid argument given';
}