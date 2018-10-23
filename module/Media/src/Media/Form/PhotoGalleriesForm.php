<?php 
/**
 * PhotoGalleriesForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class PhotoGalleriesForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'add-gallery', $options = null) 
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'created_date',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Created date',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'created_date' => [
                'filters' => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y',
                        ],
                    ],
                ],
            ],
        ];
    }
}