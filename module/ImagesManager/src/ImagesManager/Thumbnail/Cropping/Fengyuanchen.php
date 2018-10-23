<?php 
/**
 * Fengyuanchen
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace ImagesManager\Thumbnail\Cropping;

class Fengyuanchen extends AbstractAdapter
{
    /**
     * Construct
     *
     * @param Traversable|array $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options) 
    {
        if (isset($options['rotate'])) {
            $options['rotate_degree'] = $options['rotate'];
            unset($options['rotate']);
        }

        $map = [
            'rotate' => 'rotate_degree',
            'width'  => 'crop_width',
            'height' => 'crop_height',
            'x'      => 'offset_x',
            'y'      => 'offset_y',
        ];

        foreach ($map as $key => $value) {
            if (isset($options[$key])) {
                $options[$value] = $options[$key];
                unset($options[$key]);
            }
        }

        if ($options['rotate_degree'] != 0) {
            $options['rotate_degree'] *= -1;
        }

        return parent::setOptions($options);
    }
}