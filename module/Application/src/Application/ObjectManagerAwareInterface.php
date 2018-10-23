<?php 
/**
 * Doctrine object manager aware interface
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application;

use Doctrine\Common\Persistence\ObjectManager;

interface ObjectManagerAwareInterface
{
    /**
     * Sets object manager
     *
     * @param ObjectManager $objectManager
     * @return ObjectManagerAwareInterface
     */
    public function setObjectManager(ObjectManager $objectManager);
}