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
use Application\ObjectManagerAwareTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Club;
use Soccer\Entity\Season;
use Soccer\Entity\Stage;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * SeasonsForm
 */
class SeasonsForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $inputFilterSpecification = [];

    /**
     * @inheritDoc
     */
    public function __construct($name = 'seasons-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Initiates basic form elements
     */
    public function init()
    {
        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'label' => 'Label',
                    'locales' => $this->getOption('locales')
                ]
            )
        );

        $this->add(
            [
                'name' => 'type',
                'type' => 'Select',
                'options' => [
                    'label' => 'Type',
                    'value_options' => [
                        Season::TYPE_LEAGUE       => 'League',
                        Season::TYPE_CHAMPIONSHIP => 'Championship',
                        Season::TYPE_CUP          => 'Cup',
                        Season::TYPE_EXHIBITION   => 'Exhibition games',
                    ],
                ],
            ]
        );

        $this->add(
            [
                'name' => 'clubs',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Club::class,
                    'property'       => 'name'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => [
                                'alias' => 'ASC'
                            ],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'label'              => 'Participants',
                ],
                'attributes' => [
                    'multiple' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'visible',
                'type' => 'Checkbox',
                'options' => [
                    'label' => 'Visible',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'winsBy',
                'type' => 'Select',
                'options' => [
                    'label' => 'Consider winner by',
                    'value_options' => [
                        Stage::WINS_BY_GOALS_DIFFERENCE => 'Goals difference',
                        Stage::WINS_BY_MATCHES          => 'Matches between clubs',
                    ],
                ],
            ]
        );

        $this->inputFilterSpecification = [
            'type' => [
                'required' => true,
            ],
            'visible' => [
                'required' => true,
            ],
            'winsBy' => [
                'required' => false,
            ],
        ];
    }

    /**
     * Add elements to form if season type league selected
     */
    public function addElementsIfLeague()
    {
        $this->inputFilterSpecification['winsBy'] = [
            'required' => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        if ($data['clubs']) {
            $clubs = $this->getObjectManager()
                ->getRepository(Club::class)
                ->findBy(['id' => $data['clubs']]);

            if ($clubs) {
                $data['clubs'] = $clubs;
            }
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function setData($data)
    {
        if (isset($data['type']) && $data['type'] == Season::TYPE_LEAGUE) {
            $this->addElementsIfLeague();
        }

        if (isset($data['type']) && $data['type']) {
            switch ($data['type']) {
                case Season::TYPE_LEAGUE:
                    $this->addElementsIfLeague();
                break;
                case Season::TYPE_EXHIBITION:
                    if ($this->getInputFilter()) {
                        $this->getInputFilter()->get('clubs')
                            ->setRequired(false);
                    }
                break;
            }
        }

        parent::setData($data);
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpecification;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @inheritDoc
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->init();
        return $this;
    }
}