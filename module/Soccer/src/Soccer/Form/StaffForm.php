<?php
/**
 * StaffForm
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DateTime;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class StaffForm
    extends Form
    implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var int inClubSince min value max is current year
     */
    const INCLUB_MIN = 1986;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * StaffForm constructor.
     * @param $name
     * @param array $options
     */
    public function __construct($name = 'staff-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Initiates form elements
     */
    public function init()
    {
        $this->add(
            [
                'name'    => 'photo',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Photo',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'firstname',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Firstname',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'lastname',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Lastname',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'birthDate',
                'type' => 'Text',
                'options' => [
                    'label' => 'Birth date',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'inClubSince',
                'type' => 'Select',
                'options' => [
                    'label'         => 'In club since',
                    'value_options' => array_combine(
                        ($range = range((int) date('Y'), self::INCLUB_MIN, -1)),
                        $range
                    ),
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'position',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Position',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'group',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => 'Soccer\Entity\StaffGroup',
                    'property'        => 'name',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['name' => 'ASC'],

                            // Use key 'sort' if using ODM
                            'sort'  => []
                        ],
                    ],
                    'label'              => 'Group',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select group',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        if (isset($data['group'])) {
            $data['group'] = $this->getObjectManager()
                ->getRepository(
                    $this->get('group')
                    ->getOption('target_class')
                )
                ->find($data['group']);
        }

        if (isset($data['birthDate']) && $data['birthDate'] instanceof DateTime) {
            $data['birthDate']->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     * @return $this
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        // initiate form here because form needs object manager
        $this->init();

        return $this;
    }

    public function getInputFilterSpecification()
    {
        return [
            'photo' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Uri',
                    ],
                ],
            ],
            'firstname' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 32,
                        ],
                    ],
                ],
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ],
                    [
                        'name' => 'StripTags'
                    ],
                ],
            ],
            'lastname' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 32,
                        ],
                    ],
                ],
                'filters' => [
                    [
                        'name' => 'StringTrim'
                    ],
                    [
                        'name' => 'StripTags'
                    ],
                ],
            ],
            'birthDate' => [
                'required' => true,
                'validators' => [
                    [
                        'name'    => 'Date',
                        'options' => [
                            'format' => 'd.m.Y',
                        ],
                    ],
                ],
            ],
            'inClubSince' => [
                'required' => true,
            ],
            'position' => [
                'required' => true,
                'filters'    => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 64,
                        ],
                    ]
                ],
            ],
            'group' => [
                'required' => true,
            ],
        ];
    }
}