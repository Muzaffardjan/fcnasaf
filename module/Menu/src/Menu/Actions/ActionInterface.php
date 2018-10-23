<?php 
/**
 * Menu ActionInterface
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Actions;

use Zend\Uri\Uri;

interface ActionInterface 
{
    /**
     * Gets action title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Gets actions api route name
     *
     * @return string
     */
    public function getRouteName();

    /**
     * Gets actions api route params
     *
     * @return array
     */
    public function getRouteParams();

    /**
     * Gets actions api route options
     *
     * @return array
     */
    public function getRouteOptions();
}