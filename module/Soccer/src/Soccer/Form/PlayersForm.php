<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Validator\NoObjectExists;
use Soccer\Entity\Player;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Exception\BadMethodCallException;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Between;
use Zend\Validator\Regex;

/**
 * PlayersForm
 */
class PlayersForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var bool
     */
    protected $update = false;

    /**
     * PlayersForm constructor.
     * @param int|null|string $name
     * @param array $options
     */
    public function __construct($name = 'soccer-players-form', array $options = [])
    {
        if (isset($options['locales'])) {
            $this->setLocales($options['locales']);
        }

        parent::__construct($name, $options);
    }

    /**
     * Prepares form
     */
    public function init()
    {
        $this->add(
            [
                'name' => 'alias',
                'type' => 'Text',
                'options' => [
                    'label' => 'Alias name for access purposes',
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'firstname',
                [
                    'locales' => $this->getLocales(),
                    'label'   => 'First name',
                ]
            )
        );

        $this->add(
            new LocalesTextFieldset(
                'lastname',
                [
                    'locales' => $this->getLocales(),
                    'label'   => 'Last name',
                ]
            )
        );

        $this->add(
            new LocalesTextFieldset(
                'thirdname',
                [
                    'locales' => $this->getLocales(),
                    'label'   => 'Third name',
                    'element_specification' => [
                        'required' => false,
                        'validators' => [
                            [
                                'name' => 'StringLength',
                                'options' => [
                                    'max' => 64,
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->add(
            [
                'name' => 'ended_career',
                'type' => 'checkbox',
                'options' => [
                    'label'              => 'Ended career',
                    'use_hidden_element' => false,
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'birthDate',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Birth date',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'height',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Height',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'weight',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Weight',
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'country',
                [
                    'locales' => $this->getLocales(),
                    'label'   => 'Country',
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputFilterSpecification()
    {
        $specification = [
            'alias'     => [
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
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            'ended_career' => [
                'required' => false,
            ],
            'birthDate' => [
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
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y',
                        ],
                    ],
                ],
            ],
            'height' => [
                'required' => false,
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
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '#[0-9]+#',
                            'messages' => [
                                Regex::NOT_MATCH => 'Invalid height',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => 100,
                            'max' => 250,
                            'messages' => [
                                Between::NOT_BETWEEN => 'No, no, no it is can not be truth',
                            ],
                        ],
                    ],
                ],
            ],
            'weight' => [
                'required' => false,
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
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '#[0-9]+#',
                            'messages' => [
                                Regex::NOT_MATCH => 'Invalid weight',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => 25,
                            'max' => 150,
                        ],
                        'messages' => [
                            Between::NOT_BETWEEN => 'No, no, no it is can not be truth',
                        ],
                    ],
                ],
            ],
        ];

        if (!$this->isUpdate()) {
            $specification['alias']['validators'][] = [
                'name' => 'DoctrineModule\Validator\NoObjectExists',
                'options' => [
                    'object_repository' => $this->getObjectManager()->getRepository(Player::class),
                    'fields'            => 'alias',
                    'messages'          => [
                        NoObjectExists::ERROR_OBJECT_FOUND => "Player with this alias already exists. Maybe you forgot that you already added this player?",
                    ],
                ],
            ];
        }

        return $specification;
    }

    /**
     * @return boolean
     */
    public function isUpdate()
    {
        return $this->update;
    }

    /**
     * @param bool $update
     * @return self
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * @param ObjectManager $objectManager
     * @return self
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $this->init();

        return $this;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @return array
     * @throws BadMethodCallException
     */
    public function getLocales()
    {
        if (null === $this->locales) {
            throw new BadMethodCallException(
                "Undefined \$locales array expected."
            );
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