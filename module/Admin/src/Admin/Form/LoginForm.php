<?php 
/**
 * Admin login form
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'admin-login', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'login',
                'type' => 'Text',
                'options' => [
                    'label'             => 'Username',
                    'label_attributes'  => [
                        'class' => 'sr-only',
                    ],
                ],  
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'password',
                'type' => 'Password',
                'options' => [
                    'label'             => 'Password',
                    'label_attributes'  => [
                        'class' => 'sr-only',
                    ],
                ],  
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'csrf',
                'type' => 'Csrf',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            'login'     => [
                'required'  => true,
                'filters'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 4,
                            'max' => 32,
                        ],
                    ],
                ],
            ],
            'password'  => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'min' => 7,
                            'max' => 40,
                        ],
                    ],
                ],
            ],
            'csrf'      => [
                'required' => true,
            ],
        ];  
    }
}