<?php 
/**
 * Abstract controller
 * Helps little to prevent code clonning
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Log\Logger;

use Application\LoggerAwareInterface;

/**
 * Class AbstractController
 * @method \Application\Controller\Plugin\ApiCall apiCall()
 * @method \Application\Controller\Plugin\Locale locale()
 * @method string translate(string $message, string $textDomain = 'default', string $locale = null)
 * @method \Zend\Http\Request getRequest()
 * @method \Zend\Http\Response getResponse()
 */
abstract class AbstractController 
    extends AbstractActionController
    implements LoggerAwareInterface
{
    /**
     * @var Logger $logger
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets application logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Parent class has this action by default
     * Seal it it will give 404 repose 
     * if child class didn't overwrite it 
     *
     * @overwrite
     */
    public function indexAction()
    {
        $this->getResponse()->setStatusCode(404);
        
        return;
    }
}