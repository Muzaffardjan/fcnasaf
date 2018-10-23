<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Soccer\Exception\RuntimeException;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * PlayerPositionsForm
 */
class PlayerPositionsForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    protected $locales;
    
    public function __construct($name = 'player-positions-form', array $options = [])
    {
        parent::__construct($name, $options);
        
        if ($this->getOption('locales')) {
            $this->setLocales($this->getOption('locales'));
        }

        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'label'   => 'Label',
                    'locales' => $this->getLocales(),
                ]
            )
        );

        $this->add(
            new LocalesTextFieldset(
                'pluralLabel',
                [
                    'label'   => 'Plural label',
                    'locales' => $this->getLocales(),
                ]
            )
        );

        $this->add(
            [
                'name' => 'order',
                'type' => 'Text',
                'options' => [
                    'label' => 'Order in lists',
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
            'order' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validator' => [
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => -255,
                            'max' => 255,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     * @throws RuntimeException
     */
    public function getLocales()
    {
        if (null === $this->locales) {
            throw new RuntimeException("Undefined locales");
        }
        
        return $this->locales;
    }

    /**
     * @param array $locales
     * @return self
     */
    public function setLocales(array $locales)
    {
        $this->locales = $locales;

        return $this;
    }
}