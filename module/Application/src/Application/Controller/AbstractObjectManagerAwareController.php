<?php 
/**
 * Abstract controller
 * Helps little to prevent code clonning
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Application\Controller;

use Zend\Mvc\MvcEvent;
use Doctrine\Common\Persistence\ObjectManager;

use Application\ObjectManagerAwareInterface;

abstract class AbstractObjectManagerAwareController 
extends AbstractController
implements ObjectManagerAwareInterface 
{
    /**
     * @const string after dispatch flush event
     */
    const EVENT_FLUSH = 'flush.objectmanager';

    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;

    /**
     * @var bool 
     */
    protected $needFlush = true;

    /**
     * Adding flush event after dispatch
     *
     * {@inheritdoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        $dispatched     = parent::onDispatch($e);
        if (true === $this->isFlushNeeded()) {        
            $eventManager   = $e->getApplication()->getEventManager();
            $event          = $this->getEvent();

            $event->setName(self::EVENT_FLUSH);
            // event target is current controller
            $event->setTarget($this);

            // Trigger flush event
            $eventManager->trigger($event);
        }

        return $dispatched;
    }

    /**
     * Indicates whatever trigger flush.objectmanager event
     * flush.objectmanager event flushes objectmager
     * If controller only reads db it will be faster if this method returns false
     *
     * @return bool
     */
    public function isFlushNeeded()
    {
        return $this->needFlush;
    }

    /**
     * Sets if controller needs flush object manager after dispatch
     *
     * @param   bool $need
     * @return  self
     */
    public function setNeedFlush($need)
    {
        $this->needFlush = (bool) $need;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        // Return instance
        return $this;
    }

    /**
     * Gets doctrine object manager instance
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}