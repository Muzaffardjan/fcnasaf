<?php 
/**
 * Menu Action
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

class Action extends AbstractAction
{
    /**
     * Construct
     *
     * @param string $title
     * @param string $routeName
     * @param string $routeParams
     * @param string $routeOptions
     */
    public function __construct(
        $title = null, 
        $routeName = null, 
        $routeParams = null, 
        $routeOptions = null
    ) {
        if (null !== $title) {
            $this->setTitle($title);
        }

        if (null !== $routeName) {
            $this->setRouteName($routeName);
        }

        if (null !== $routeParams) {
            $this->setRouteParams($routeParams);
        } else {
            $this->setRouteParams([]);
        }

        if (null !== $routeOptions) {
            $this->setRouteOptions($routeOptions);
        } else {
            $this->setRouteOptions([]);
        }
    }
}