<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Soccer\Entity\MatchStatistics;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * MatchStatisticsForm
 */
class MatchStatisticsForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'match-statistics', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'type',
                'type' => 'Select',
                'options' => [
                    'label' => 'Value type',
                    'value_options' => [
                        MatchStatistics::TYPE_PERCENT => 'Percent',
                        MatchStatistics::TYPE_QUANTITY => 'Quantity'
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name' => 'hostValue',
                'type' => 'Text',
                'options' => [
                    'label' => 'Host club value',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'guestValue',
                'type' => 'Text',
                'options' => [
                    'label' => 'Guest club value',
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'label'   => 'Label',
                    'locales' => $this->getOption('locales'),
                    'locale'  => $this->getOption('locale'),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'type' => [
                'required' => true,
            ],
            'hostValue' => [
                'required' => true,
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
                        'name' => 'Digits'
                    ],
                ],
            ],
            'guestValue' => [
                'required' => true,
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
                        'name' => 'Digits'
                    ],
                ],
            ],
        ];
    }
}