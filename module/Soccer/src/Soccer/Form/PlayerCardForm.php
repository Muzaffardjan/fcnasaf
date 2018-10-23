<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * PlayerCardForm
 */
class PlayerCardForm extends Form implements InputFilterProviderInterface
{
    /**
     * @inheritDoc
     */
    public function __construct($name = 'player-card-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'width',
                'type' => 'Text',
                'options' => [
                    'label' => 'Width',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'height',
                'type' => 'Text',
                'options' => [
                    'label' => 'Height',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'top',
                'type' => 'Text',
                'options' => [
                    'label' => 'Top offset',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'left',
                'type' => 'Text',
                'options' => [
                    'label' => 'Left offset',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'width' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Between',
                        'options' => [
                            'max' => 480,
                            'min' => 0,
                        ],
                    ]
                ],
            ],
            'height' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Between',
                        'options' => [
                            'max' => 1080,
                            'min' => 0,
                        ],
                    ]
                ],
            ],
            'top' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Between',
                        'options' => [
                            'max' => 1080,
                            'min' => -1080,
                        ],
                    ]
                ],
            ],
            'left' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Between',
                        'options' => [
                            'max' => 480,
                            'min' => -480,
                        ],
                    ]
                ],
            ],
        ];
    }

}