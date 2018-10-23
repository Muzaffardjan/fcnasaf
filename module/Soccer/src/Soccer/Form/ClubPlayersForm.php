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
use Soccer\Entity\Club;
use Soccer\Entity\Composition;
use Soccer\Entity\Player;
use Soccer\Entity\PlayerPosition;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Between;

/**
 * ClubPlayersForm
 */
class ClubPlayersForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var Club
     */
    protected $club;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'club-players-form', array $options = [])
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
                'name' => 'player',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => Player::class,
                    'property'        => 'alias',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findNotPlaying',
                    ],
                    'label'              => 'Player',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select player',
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
                'name'    => 'since',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Came',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'until',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Leave',
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

        if (isset($data['player']) && $data['player']) {
            $player = $this->getObjectManager()->getRepository(Player::class)
                ->find($data['player']);

            if ($player) {
                $data['player'] = $player;
            }
        }

        if (isset($data['position']) && $data['position']) {
            $position = $this->getObjectManager()->getRepository(PlayerPosition::class)
                ->find($data['position']);

            if ($position) {
                $data['position'] = $position;
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
            'player' => [
                'required' => true,
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
            'position' => [
                'required' => true,
            ],
            'since' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y'
                        ],
                    ],
                ],
            ],
            'until' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y'
                        ],
                    ],
                ],
            ],
        ];
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

        return $this;
    }

    /**
     * @return Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * @param Club $club
     * @return self
     */
    public function setClub(Club $club)
    {
        $this->club = $club;

        $this->init();

        return $this;
    }
}