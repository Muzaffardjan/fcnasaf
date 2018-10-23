<?php
/**
 * StaffGroupForm
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class StaffGroupForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'staff-group-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'name',
                'type' => 'Text',
                'options' => [
                    'label' => 'Group name',
                ],
            ]
        );
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 64,
                        ],
                    ]
                ],
            ],
        ];
    }
}