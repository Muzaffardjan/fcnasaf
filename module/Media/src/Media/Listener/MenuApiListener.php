<?php 
/**
 * MenuApiListener event listener
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Menu\Event\MenuEvent;
use Menu\Entity\Container;
use Menu\Actions\ActionInterface as MenuActionInterface;
use Menu\Actions\Action as MenuAction;
use Menu\Actions\ActionsContainer as MenuActionsContainer;

class MenuApiListener extends AbstractListenerAggregate
{

    const TITLE_CONTAINER = 'Media';
    const TITLE_GALLERIES = 'Galleries';
    const TITLE_GALLERY   = 'Gallery';
    const TITLE_VIDEOS    = 'Videos';
    const TITLE_VIDEO     = 'Video';

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
            $this->getGalleriesElementAction(
                self::TITLE_GALLERIES
            )
        )
        ->addAction(
            $this->getGalleryElementAction(
                self::TITLE_GALLERY
            )
        )
        ->addAction(
            $this->getVideosElementAction(
                self::TITLE_VIDEOS
            )
        )
        ->addAction(
            $this->getVideoElementAction(
                self::TITLE_VIDEO
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
    public function getGalleriesElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/media/gallery-menu-api')
        ->setRouteParams(
            [
                'action'    => 'galleries',
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
    public function getGalleryElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/media/gallery-menu-api')
        ->setRouteParams(
            [
                'action'    => 'gallery',
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
    public function getVideosElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/media/video-menu-api')
        ->setRouteParams(
            [
                'action'    => 'videos',
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
    public function getVideoElementAction($title)
    {
        $action = new MenuAction();

        $action->setRouteName('app/admin/media/video-menu-api')
        ->setRouteParams(
            [
                'action'    => 'video',
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