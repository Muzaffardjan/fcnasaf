<?php 
/**
 * PhotosForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class PhotosForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'photos-form', $options = null) 
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'shot_date',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Shot date',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'shot_date' => [
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