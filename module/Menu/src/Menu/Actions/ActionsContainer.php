<?php 
/**
 * Menu ActionsContainer
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

use Menu\Exception;

class ActionsContainer extends AbstractActionsContainer
{
    /**
     * @param   array $options
     * @return  ActionsContainer
     * @throws  Exception\RuntimeException
     */
    public static function factory(array $options)
    {
        if (!isset($options['title'])) {
            throw new Exception\RuntimeException(
                'Container title is required.'
            );
        }

        $actions = new static();
        $actions->setTitle($options['title']);
    
        if (isset($options['actions'])) {
            foreach ($options['actions'] as $actionOptions) {
                $action = new Action();
                
                $action->setTitle($actionOptions['title']);
                
                if (isset($actionOptions['title']) && isset($actionOptions['route']['name'])) {
                    $action->setTitle($actionOptions['title'])
                        ->setRouteName($actionOptions['route']['name']);

                    if (isset($actionOptions['route']['params'])) {
                        $action->setRouteParams($actionOptions['route']['params']);
                    }

                    if (isset($actionOptions['route']['options'])) {
                        $action->setRouteParams($actionOptions['route']['options']);
                    }

                    $actions->addAction($action);
                } else {
                    throw new Exception\RuntimeException(
                        'Required fields undefined.'
                    );
                }
            }
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteWithParams()
    {
        return null;
    }
}