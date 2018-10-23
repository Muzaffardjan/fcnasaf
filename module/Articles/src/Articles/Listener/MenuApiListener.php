<?php 
/**
 * MenuApiListener event listener
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Menu\Event\MenuEvent;
use Menu\Entity\Container;
use Menu\Actions\ActionInterface as MenuActionInterface;
use Menu\Actions\Action as MenuAction;
use Menu\Actions\ActionsContainer as MenuActionsContainer;

class MenuApiListener extends AbstractListenerAggregate
{

    const TITLE_CONTAINER     = 'Articles';
    const TITLE_ARTICLE       = 'Article';
    const TITLE_CATEGORY      = 'Category';
    const TITLE_BLOG          = 'Blog';

    /**
     * @var Container
     */
    protected $container;

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
     * Gets menu actions container
     *
     * @return MenuActionsContainer
     */
    public function getActionsContainer()
    {
        $container = new MenuActionsContainer();

        $container->setTitle(
            self::TITLE_CONTAINER
        )
        ->addAction(
            $this->getBlogElementAction(
                self::TITLE_BLOG
            )
        )
        ->addAction(
            $this->getCategoryElementAction(
                self::TITLE_CATEGORY
            )
        )
        ->addAction(
            $this->getArticleElementAction(
                self::TITLE_ARTICLE
            )
        );

        return $container;
    }
    /**
     * Gets url api action
     *
     * @param   string      $title 
     * @return  MenuAction
     */
    public function getBlogElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/articles/categories')
        ->setRouteParams(
            [
                'action'    => 'menuApiBlog',
                'container' => $this->getContainer()->getId(),
            ]
        )
        ->setTitle($title);

        return $action;
    }


    /**
     * Gets url api action
     *
     * @param   string      $title 
     * @return  MenuAction
     */
    public function getCategoryElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/articles/categories')
        ->setRouteParams(
            [
                'action'    => 'menuApi',
                'container' => $this->getContainer()->getId(),
            ]
        )
        ->setTitle($title);

        return $action;
    }

    /**
     * Gets empty element api action
     *
     * @param   string      $title 
     * @return  MenuAction
     */
    public function getArticleElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/articles/articles')
        ->setRouteParams(
            [
                'action'    => 'menuApi',
                'container' => $this->getContainer()->getId(),
            ]
        )
        ->setTitle($title);

        return $action;
    }

    /**
     * Gets the value of container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the value of container.
     *
     * @param Container $container the container
     *
     * @return self
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}