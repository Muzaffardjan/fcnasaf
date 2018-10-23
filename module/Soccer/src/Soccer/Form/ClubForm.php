<?php
/**
 * ClubForm
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Form;


use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\ObjectExists;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Between;

class ClubForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var bool
     */
    protected $update;

    /**
     * ClubForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'soccer-club-form', array $options = [])
    {
        if (isset($options['locales'])) {
            $this->setLocales($options['locales']);
        }

        parent::__construct($name, $options);
    }

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
                'name',
                [
                    'label'   => 'Club name',
                    'locales' => $this->getLocales(),
                ]
            )
        );

        $this->add(
            [
                'name' => 'founded',
                'type' => 'Text',
                'options' => [
                    'label' => 'Founded',
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'tableName',
                [
                    'label' => 'Club name in results and fixtures',
                    'locales' => $this->getLocales(),
                ]
            )
        );

        $this->add(
            [
                'name' => 'parentClub',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => 'Soccer\Entity\Club',
                    'property'       => 'alias',
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
                    'label'              => 'Farm club of',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select club',
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

        if (isset($data['parentClub']) && $data['parentClub']) {
            $parentClub = $this->getObjectManager()
                ->getRepository($this->get('parentClub')->getOption('target_class'))
                ->find($data['parentClub']);

            if ($parentClub) {
                $data['parentClub'] = $parentClub;
            } else {
                $data['parentClub'] = null;
            }
        } else {
            $data['parentClub'] = null;
        }

        return $data;
    }


    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        $specification = [
            'alias' => [
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
                        'name'    => 'StringLength',
                        'options' => [
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            'founded' => [
                'required' => false,
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
                        'name'    => 'Between',
                        'options' => [
                            'min' => 1900,
                            'max' => date('Y'),
                            'messages' => [
                                Between::NOT_BETWEEN => 'Impossible founded year',
                            ],
                        ],
                    ],
                ],
            ],
            'parentClub' => [
                'required' => false,
            ],
        ];

        if (!$this->isUpdate()) {
            $specification['alias']['validators'][] = [
                'name' => 'DoctrineModule\Validator\NoObjectExists',
                'options' => [
                    'object_repository' => $this->getObjectManager()->getRepository(
                        $this->get('parentClub')->getOption('target_class')
                    ),
                    'fields'            => 'alias',
                    'messages'          => [
                        NoObjectExists::ERROR_OBJECT_FOUND => "Club with this alias already exists. Maybe you forgot that you already added this club?",
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
}