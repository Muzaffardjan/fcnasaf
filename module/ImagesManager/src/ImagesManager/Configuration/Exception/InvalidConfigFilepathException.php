<?php 
/**
 * Configuration exception
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Configuration\Exception;

use \Exception;
use ImagesManager\Exception\ExceptionInterface as ImagesManagerException;

class InvalidConfigFilepathException 
extends Exception 
implements ImagesManagerException
{
    /**
     * @var string $message
     */
    public $message = 'Invalid config filepath provided!';
}