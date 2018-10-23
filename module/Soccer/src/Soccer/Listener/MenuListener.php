<?php
/**
 * MenuListener menu events listener
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Menu\Event\MenuEvent;
use Menu\Entity\Container;
use Menu\Actions\ActionInterface as MenuActionInterface;
use Menu\Actions\Action as MenuAction;
use Menu\Actions\ActionsContainer as MenuActionsContainer;

class MenuListener extends AbstractListenerAggregate
{
    /**
     * @const string Actions container label
     */
    const TITLE_CONTAINER = 'Soccer';

    /**
     * @const string Staff group action label
     */
    const TITLE_STAFF_GROUP = 'Staff group';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var MenuActionsContainer
     */
    protected $actionsContainer;
    
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MenuEvent::GET_ACTIONS,
            [$this, 'triggered']
        );
    }

    /**
     * Event callback
     *
     * Returns route params to url action api
     *
     * @return MenuActionInterface
     */
    public function triggered(MenuEvent $e)
    {
        $this->setContainer($e->getTarget());

        return $this->getActionsContainer();
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     * @return MenuListener
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return MenuActionsContainer
     */
    public function getActionsContainer()
    {
        if (null === $this->actionsContainer) {
            $this->actionsContainer = MenuActionsContainer::factory(
                $this->getActionOptions()
            );
        }

        return $this->actionsContainer;
    }

    /**
     * @param MenuActionsContainer $actionsContainer
     * @return MenuListener
     */
    public function setActionsContainer(MenuActionsContainer $actionsContainer)
    {
        $this->actionsContainer = $actionsContainer;
        return $this;
    }

    /**
     * @return array Container factory parameters
     */
    private function getActionOptions()
    {
        return [
            'title'     => self::TITLE_CONTAINER,
            'actions'   => [
                [
                    'title' => self::TITLE_STAFF_GROUP,
                    'route' => [
                        'name'   => 'app/admin/soccer/staff-group',
                        'params' => [
                            'id'     => $this->getContainer()->getId(),
                            'action' => 'menu',
                        ],
                    ],
                ],
            ],
        ];
    }
}