<?php 
/**
 * PagesForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class PagesForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array 
     */
    protected $applicationConfig;

    public function __construct($name = 'pages-form', $options = null)
    {
        parent::__construct($name, $options);

        if (isset($options['application_config'])) {
            $this->setApplicationConfig(
                $options['application_config']
            );
        }

        $this->add(
            [
                'name'    => 'locale',
                'type'    => 'Select',
                'options' => [
                    'label'        => 'Language',
                    'empty_option' => 'Unspecified',
                    'value_options'=> $this->getApplicationConfig('translator')['locales']
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'label',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Label',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'title',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Title',
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
                'name'    => 'fragment',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Fragment',
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

        $this->add(
            [
                'name'    => 'visible',
                'type'    => 'Checkbox',
                'options' => [
                    'label' => 'Visible',
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

    public function getInputFilterSpecification()
    {
        return [
            'locale' => [
                'required' => false,
            ],
            'label' => [
                'required' => true,
            ],
            'title' => [
                'required' => false,
            ],
            'fragment' => [
                'required' => false,
            ],
            'target' => [
                'required' => false,
            ],
            'visible' => [
                'required' => false,
            ],
            'url'     => [
                'required' => false,
            ],
        ];  
    }
}