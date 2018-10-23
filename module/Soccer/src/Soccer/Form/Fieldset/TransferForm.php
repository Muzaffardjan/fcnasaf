<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form\Fieldset;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Club;
use Soccer\Entity\PlayerPosition;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Between;

/**
 * TransferForm
 */
class TransferForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $locales;

    public function __construct($name = 'transfer-form', array $options = [])
    {
        if (isset($options['locales'])) {
            $this->setLocales($options['locales']);
        }

        parent::__construct($name, $options);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->add(
            [
                'name'    => 'date',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Date',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'number',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Player number',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'position',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => PlayerPosition::class,
                    //'property'        => 'label'.str_replace('-', '_', $this->getOption('locale')),
                    'property'        => 'label'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => [],

                            // Use key 'sort' if using ODM
                            'sort'  => []
                        ],
                    ],
                    'label'              => 'Position',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'club',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => Club::class,
                    'property'        => 'alias',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['alias' => 'ASC'],

                            // Use key 'sort' if using ODM
                            'sort'  => []
                        ],
                    ],
                    'label'              => 'To club',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        if ($data['club']) {
            $club = $this->getObjectManager()
                ->getRepository(Club::class)
                ->find($data['club']);

            $data['club'] = $club;
        }

        if ($data['position']) {
            $pp = $this->getObjectManager()
                ->getRepository(PlayerPosition::class)
                ->find($data['position']);
            
            if ($pp) {
                $data['position'] = $pp;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'date' => [
                'required' => true,
                'filters' => [
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
                    ]
                ],
            ],
            'number' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Between',
                        'options' => [
                            'min' => 0,
                            'max' => 999,
                            'messages' => [
                                Between::NOT_BETWEEN => 'Invalid player number',
                            ],
                        ],
                    ],
                ],
            ],
            'club' => [
                'required' => true,
            ],
            'position' => [
                'required' => true,
            ],
        ];
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
     * @return self
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->init();
        return $this;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
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