<?php 
/**
 * CroppingAdapterInterface
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace ImagesManager\Thumbnail\Cropping;

interface AdapterInterface 
{    
    /**
     * Sets offset x of an image
     *
     * @param float $offsetX
     */
    public function setOffsetX($offsetX);

    /**
     * Gets starting point of obsissa arrow of image
     *
     * @return float
     */
    public function getOffsetX();

    /**
     * Sets offset y of an image
     *
     * @param float $offsetY
     */
    public function setOffsetY($offsetY);

    /**
     * Gets starting point of ordinata arrow of image
     *
     * @return float
     */
    public function getOffsetY();

    /**
     * Sets rotate degree of an image
     *
     * @param float $rotate
     */
    public function setRotateDegree($rotate);

    /**
     * Gets rotate degree of an image
     *
     * @return float
     */
    public function getRotateDegree();

    /**
     * Sets crop width
     *
     * @param float $cropWidth
     */
    public function setCropWidth($cropWidth);

    /**
     * Gets crop width
     *
     * @return float
     */
    public function getCropWidth();

    /**
     * Sets crop height
     *
     * @param float $cropHeight
     */
    public function setCropHeight($cropHeight);

    /**
     * Gets crop height
     *
     * @return float
     */
    public function getCropHeight();
}