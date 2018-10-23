<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Soccer\Entity\Stage;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * StagesForm
 */
class StagesForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    protected $inputFilterSpecification;

    /**
     * @inheritDoc
     */
    public function __construct($name = 'stages-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'type',
                'type' => 'Select',
                'options' => [
                    'label' => 'Type',
                    'value_options' => [
                        Stage::TYPE_GROUP    => 'Group play',
                        Stage::TYPE_PLAY_OFF => 'Play off',
                    ],
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'locales' => $this->getOption('locales'),
                    'label'   => 'Label',
                ]
            )
        );

        $this->add(
            [
                'name' => 'winsBy',
                'type' => 'Select',
                'options' => [
                    'label' => 'Decide winner by',
                    'value_options' => [
                        Stage::WINS_BY_GOALS_DIFFERENCE => 'Goals difference',
                        Stage::WINS_BY_MATCHES          => 'Matches between teams',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name' => 'matchesCount',
                'type' => 'Text',
                'options' => [
                    'label' => 'Matches count in series',
                    'value' => 2,
                ],
            ]
        );

        $this->inputFilterSpecification = [
            'type' => [
                'required' => true,
            ],
            'winsBy' => [
                'required' => false,
            ],
            'matchesCount' => [
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => 1,
                            'max' => 10,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function normalMode()
    {
        $this->inputFilterSpecification['type']['required'] = true;

        return $this;
    }

    public function editMode()
    {
        $this->inputFilterSpecification['type']['required'] = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setData($data)
    {
        if ($data['type'] == Stage::TYPE_PLAY_OFF) {
            $this->inputFilterSpecification['matchesCount']['required'] = true;
            $this->inputFilterSpecification['winsBy']['required']       = false;
        } elseif ($data['type'] == Stage::TYPE_GROUP) {
            $this->inputFilterSpecification['winsBy']['required']       = true;
            $this->inputFilterSpecification['matchesCount']['required'] = false;
        }

        return parent::setData($data);
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpecification;
    }
}