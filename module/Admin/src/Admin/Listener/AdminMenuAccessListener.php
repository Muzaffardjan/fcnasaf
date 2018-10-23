<?php 
/**
 * Admin menu rbac permissions listener
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Admin\Listener;

use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceManager;

class AdminMenuAccessListener 
{
    public function isAllowed(Event $e) {
        $e->stopPropagation();

        $accepted   = true;
        $params     = $e->getParams();
        $page       = $params['page'];
        $identity   = $e->getTarget()->getView()->plugin('isGranted');

        $permission = $page->getPermission();

        if ($permission) {
            $accepted = $identity($permission);
        }

        return $accepted;
    }   
}