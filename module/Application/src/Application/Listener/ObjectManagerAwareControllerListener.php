<?php  
/**
 * Listener for ObjectManagerAwareInterface controllers
 * flushes object manager's state after cotroller's dipatch ends
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Application\Listener;

use Zend\Mvc\MvcEvent;
use Zend\Log\Logger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\DBALException;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

use Application\ObjectManagerAwareInterface;
use Application\LoggerAwareInterface;
use Application\Controller\AbstractObjectManagerAwareController as Controller;

class ObjectManagerAwareControllerListener
    extends AbstractListenerAggregate
    implements ObjectManagerAwareInterface,
               LoggerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Controller::EVENT_FLUSH, [$this, 'onAfterDispatch']);
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectManager(ObjectManager $oManager)
    {
        $this->objectManager = $oManager;

        return $this;
    }

    /**
     * Gets object manager
     *
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * {@inheritdoc}
     */ 
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
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
     * Flushes state of object manager
     * 
     * @param MvcEvent $event
     */
    public function onAfterDispatch(MvcEvent $event)
    {
        $target = $event->getTarget();

        // target must be ObjectManagerAware
        if ($target instanceof ObjectManagerAwareInterface) {
            $objectManager  = $this->getObjectManager();
            $unitOfWork     = $objectManager->getUnitOfWork();

            $this->getLogger()->debug(
                __CLASS__ . ": Flushing object manager."
            );

            try {
                // Flush object manager
                $objectManager->flush();
            } catch (DBALException $e) {
                // Write critial log
                $this->getLogger()->crit(
                    sprintf(
                        __CLASS__ . 
                        ": Failed to flush object manager with message: %s",
                        str_replace("\n", " ", $e->getMessage())
                    )
                );

                // Zf2 wouldn't notice this exception anyway
                // mean that it's after dispatch
                // have to trigger error
                // Fatal error will be triggered
                trigger_error(
                    sprintf(
                        __CLASS__ . 
                        ": Failed to flush object manager with message: %s",
                        $e->getMessage()
                    ),
                    E_USER_ERROR
                );
            }
        }
    }
}
