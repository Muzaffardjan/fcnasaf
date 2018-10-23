<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\Exception;

/**
 * InvalidConfigException
 */
class InvalidConfigException extends \Exception implements ExceptionInterface
{
    public $message = 'Invalid config.';
}