<?php 
/**
 * Menu AbstractActionsContainer
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

abstract class AbstractActionsContainer 
extends AbstractAction
implements ActionInterface, ActionsContainerInterface
{
    /**
     * @var array
     */
    protected $actions;

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * {@inheritdoc}
     */
    public function setActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }
}