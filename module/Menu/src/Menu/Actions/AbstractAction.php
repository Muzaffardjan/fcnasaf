<?php 
/**
 * Menu AbstractAction
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

use Zend\Uri\Uri;

abstract class AbstractAction implements ActionInterface
{
    /**
     * @var string 
     */
    protected $title;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParams;

    /**
     * @var array
     */
    protected $routeOptions;

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param   string $title
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->route;
    }

    /**
     * Sets actions api route name
     *
     * @param   string $name
     * @return  self
     */
    public function setRouteName($name) 
    {
        $this->route = (string) $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Sets actions api route params
     *
     * @param   array $params
     * @return  self
     */
    public function setRouteParams(array $params) 
    {
        $this->routeParams = $params;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteOptions()
    {
        return $this->routeOptions;
    }

    /**
     * Sets actions api route options
     *
     * @param   array $options
     * @return  self
     */
    public function setRouteOptions(array $options) 
    {
        $this->routeOptions = $options;

        return $this;
    }
}