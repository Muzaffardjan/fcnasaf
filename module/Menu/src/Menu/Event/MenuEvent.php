<?php 
/**
 * MenuEvent
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Event;

use Zend\EventManager\Event;
use Menu\Exception;

class MenuEvent extends Event
{
    const GET_ACTIONS = 'menu.get.actions';

    /**
     * {@inheritdoc}
     * @throws Exception\RuntimeException If given undefined event name 
     */
    public function setName($name)
    {
        if (!$this->isDefinedEventName($name)) {
            throw new Exception\RuntimeException(
                sprintf('Undefined event name \'%s\'', $name)
            );
        }

        return parent::setName($name);
    }

    protected function isDefinedEventName($name)
    {
        return in_array(
            $name, 
            [
                self::GET_ACTIONS,
            ]
        );
    }
}