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
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Repository\ClubsRepository;
use Soccer\Repository\SeriesRepository;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * SeriesForm
 */
class SeriesForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface
{
    const TYPE_OF_CLUB      = 'club';
    const TYPE_OF_SERIES    = 'series';

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $inputFilterSpecification;

    /**
     * @inheritDoc
     */
    public function __construct($name = 'series-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->add(
            [
                'name' => 'alias',
                'type' => 'Text',
                'options' => [
                    'label' => 'Alias name for access ability',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'final',
                'type' => 'Checkbox',
                'options' => [
                    'label' => 'Is this series final?',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'first',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Club::class,
                    'property'       => 'alias',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['alias' => 'ASC'],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select club',
                    'label'              => 'From clubs',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'firstPrevious',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'label'          => 'From series result',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Series::class,
                    'property'       => 'label'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findSeriesOf',
                        'params' => [
                            'stage' => $this->getStage(),
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select series',
                ],
            ]
        );
        
        $this->add(
            [
                'name' => 'second',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Club::class,
                    'property'       => 'alias',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['alias' => 'ASC'],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select club',
                    'label'              => 'From clubs',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'secondPrevious',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'label'          => 'From series result',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Series::class,
                    'property'       => 'label'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findSeriesOf',
                        'params' => [
                            'stage' => $this->getStage(),
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select series',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'winner',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Club::class,
                    'property'       => 'alias',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['alias' => 'ASC'],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select club',
                    'label'              => 'Winner',
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

        if ($data['first'] || $data['second'] || $data['winner']) {
            /**
             * @var ClubsRepository $clubsR
             */
            $clubsR = $this->getObjectManager()->getRepository(Club::class);

            if ($data['first'] && $first = $clubsR->find($data['first'])) {
                $data['first'] = $first;
            }

            if ($data['second'] && $second = $clubsR->find($data['second'])) {
                $data['second'] = $second;
            }

            if ($data['winner'] && $winner = $clubsR->find($data['winner'])) {
                $data['winner'] = $winner;
            }
        }

        if ($data['firstPrevious'] || $data['secondPrevious']) {
            /**
             * @var SeriesRepository $seriesR
             */
            $seriesR = $this->getObjectManager()->getRepository(Series::class);

            if ($data['firstPrevious'] && $first = $seriesR->find($data['firstPrevious'])) {
                $data['firstPrevious'] = $first;
            }

            if ($data['secondPrevious'] && $second = $seriesR->find($data['secondPrevious'])) {
                $data['secondPrevious'] = $second;
            }
        }

        return $data;
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param Stage $stage
     * @return self
     */
    public function setStage(Stage $stage)
    {
        $this->stage = $stage;
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
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'alias'         => [
                'required' => true,
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
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            'final' => [
                'required' => false,
            ],
            'winner' => [
                'required' => false,
            ],
            'first'  => [
                'required' => false,
            ],
            'firstPrevious'  => [
                'required' => false,
            ],
            'second'  => [
                'required' => false,
            ],
            'secondPrevious'  => [
                'required' => false,
            ],
        ];
    }
}