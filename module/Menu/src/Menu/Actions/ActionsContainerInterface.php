<?php 
/**
 * ActionsContainerInterface
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

interface ActionsContainerInterface
{
    /**
     * Gets actions of container
     *
     * @return array
     */
    public function getActions();

    /**
     * Sets actions
     *
     * @param   array $actions Array of ActionInterface's
     * @return  self
     */
    public function setActions(array $actions);

    /**
     * Adds action
     *
     * @param   ActionInterface
     * @return  self
     */
    public function addAction(ActionInterface $action);
}