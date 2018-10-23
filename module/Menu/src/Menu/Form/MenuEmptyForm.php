<?php 
/**
 * MenuUrlForm 
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class MenuEmptyForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array 
     */
    protected $applicationConfig;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'menu-url-form', $options = null)
    {
        if (isset($options['application_config'])) {
            $this->setApplicationConfig(
                $options['application_config']
            );
        }

        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'label',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Container label',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'title',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Container title',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'locale',
                'type'    => 'Select',
                'options' => [
                    'label'         => 'Language',
                    'value_options' => $this->getApplicationConfig('translator')['locales'],
                    'empty_option'  => 'Unspecified',
                ],
            ]
        );
    }

    /**
     * Gets applicaiton config
     *
     * @param   string $key
     * @return  array
     */
    public function getApplicationConfig($key = null)
    {
        if (null === $this->applicationConfig) {
            $this->applicationConfig = [];

            if (null !== $key) {
                return null;
            }

            return $this->applicationConfig;
        }

        if (isset($this->applicationConfig[$key])) {
            return $this->applicationConfig[$key];
        } else {
            return null;
        }

        return $this->applicationConfig;
    }

    /**
     * Sets application config
     *
     * @param   array $config
     * @return  self
     */
    public function setApplicationConfig(array $config)
    {
        $this->applicationConfig = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            'label' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name'    => 'StripTags',
                        'options' => [
                            'allowedTags' => [
                                'span', 
                                'i', 
                                'b', 
                                'i'
                            ],
                            'allowAttribs'=> [
                                'class',
                                'title',
                                'id',
                                'data*',
                            ],
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 255
                        ],
                    ],
                ],
            ],
            'title' => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
            'locale' => [
                'required' => false,
            ],
        ];  
    }
}