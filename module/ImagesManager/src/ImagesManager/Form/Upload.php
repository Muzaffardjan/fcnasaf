<?php 
/**
 * Images upload form
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Upload extends Form implements InputFilterProviderInterface 
{
    public function __construct($name = 'images-upload', $options = null) 
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'  => 'images',
                'type'  => 'File',
            ]
        );
    } 

    public function getInputFilterSpecification()
    {
        return [
            'images' => [
                'required'      => true,
                'validators'    => [
                    [
                        'name' => 'File\IsImage',
                    ],
                    [
                        'name'      => 'File\Size',
                        'options'   => [
                            'max'   => '1.5MB',
                        ],
                    ],
                ],
            ],
        ];
    }
}