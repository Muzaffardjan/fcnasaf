<?php
/**
 * FesCkFinderModule
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace FesCkFinderModule\Controller;

use CKSource\CKFinder\CKFinder;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\AbstractActionController;
use FesCkFinderModule\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;

/**
 * ConnectorController
 */
class ConnectorController extends AbstractActionController
{
    /**
     * @var CKFinder
     */
    protected $ckFinder;

    /**
     * ConnectorController constructor.
     *
     * @param CKFinder $ckFinder
     */
    public function __construct(CKFinder $ckFinder)
    {
        $this->ckFinder = $ckFinder;
    }

    public function indexAction()
    {
        $this->ckFinder->run();
        exit;
    }
}