<?php 
/**
 * Admin module abstact object manager aware controller
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Admin\Controller;

use Zend\Mvc\MvcEvent;

use Application\Controller\AbstractObjectManagerAwareController 
    as ApplicationAbstractObjectManagerAwareController; 

abstract class AbstractObjectManagerAwareController 
    extends ApplicationAbstractObjectManagerAwareController
{
    /**
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $event)
    {
        // Get admin module view config
        $config = $event->getApplication()->getServiceManager()->get('config');

        // if primary layout config exists
        if (isset($config['admin']['view']['layout']['primary'])) {
            // Set admin layout
            $this->layout($config['admin']['view']['layout']['primary']);
        }        

        parent::onDispatch($event);
    }
}