<?php
/**
 * Image lister folder
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Lister;

class Folder 
{
    /**
     * @var array $config
     */
    private $config = [];

    /**
     * @var SplFileInfo $file
     */
    private $file;

    /**
     * @var string $baseUrl
     */
    private $baseUrl;
}