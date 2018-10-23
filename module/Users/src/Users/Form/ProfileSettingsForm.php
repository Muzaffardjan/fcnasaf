<?php 
/**
 * User profile settings form
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputProviderInterface;

class ProfileSettingsForm extends Form implements InputProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'profile-settings', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'          => 'name',
                'type'          => 'Text',
                'options'       => [
                    'label' => 'Full name',
                ],
                'attributes'    => [
                    'required' => true,
                ],  
            ]
        );

        $this->add(
            [
                'name'          => 'password',
                'type'          => 'Password',
                'options'       => [
                    'label' => 'Password',
                ],
            ]
        );

        $this->add(
            [
                'name'          => 'password_check',
                'type'          => 'Password',
                'options'       => [
                    'label' => 'Reenter password',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInputSpecification()
    {
        return [
            'name' => [
                'required'      => true,
                'filters'       => [
                    [
                        'name'      => 'StripTags',
                    ],
                    [
                        'name'      => 'StringTrim',
                    ],
                ],
                'validators'    => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 4,
                            'max' => 24,
                        ],
                    ],
                ],
            ],
            'password' => [
                'required'      => false,
                'filters'       => [
                    [
                        'name'      => 'StringTrim',
                    ],
                ],
                'validators'    => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 7,
                        ],
                    ],
                ],
            ],
            'password_check' => [
                'required'      => false,
                'filters'       => [
                    [
                        'name'      => 'StringTrim',
                    ],
                ],
                'validators'    => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 7,
                        ],
                    ],
                    [
                        'name'      => 'Identical',
                        'options'   => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ],
        ]; 
    }
}