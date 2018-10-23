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
use Application\ObjectManagerAwareTrait;

class MenuUrlForm extends Form implements InputFilterProviderInterface
{
    use ObjectManagerAwareTrait;
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

        if (isset($options['entity_manager'])) {
            $this->setObjectManager($options['entity_manager']);
        }

        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'label',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Link label',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'title',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Link title',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'url',
                'type'    => 'Text',
                'options' => [
                    'label' => 'URL',
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

        $this->add(
            [
                'name'    => 'target',
                'type'    => 'Select',
                'options' => [
                    'label'         => 'Link target',
                    'value_options' => [
                       '_blank' => 'New tab',
                    ],
                    'empty_option'  => 'Current tab',
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
            'url' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'Uri',
                    ],
                ],
            ],
            'locale' => [
                'required' => false,
            ],
            'target' => [
                'required' => false,
            ],
        ];  
    }
}