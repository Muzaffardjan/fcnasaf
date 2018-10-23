<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Club;
use Soccer\Entity\Match;
use Soccer\Entity\Season;
use Soccer\Entity\Series;
use Soccer\Entity\Stadium;
use Soccer\Entity\Stage;
use Soccer\Entity\Tour;
use Soccer\Entity\Tournament;
use Soccer\Exception\RuntimeException;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * MatchesForm
 */
class MatchesForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'matches-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(
            [
                'name' => 'tournament',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Tournament::class,
                    'property'       => 'label'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => [],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select tournament',
                    'label'              => 'Tournament',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'season',
                'type' => 'Select',
                'options' => [
                    'label' => 'Season',
                    'empty_option' => 'Select season',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'stage',
                'type' => 'Select',
                'options' => [
                    'label' => 'Stage',
                    'empty_option' => 'Select stage',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'subStage',
                'type' => 'Select',
                'options' => [
                    'label' => 'Sub stage',
                    'empty_option' => 'Select sub stage',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'series',
                'type' => 'Select',
                'options' => [
                    'label' => 'Series',
                    'empty_option' => 'Select series',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'tour',
                'type' => 'Select',
                'options' => [
                    'label' => 'Tour',
                    'empty_option' => 'Select tour',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'status',
                'type' => 'Select',
                'options' => [
                    'label'         => 'Status',
                    'value_options' => [
                        Match::STATUS_QUEUE     => 'Queue',
                        Match::STATUS_FINISHED  => 'Finished',
                    ],
                ],
            ]
        );

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
                'name'    => 'time',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Time',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'stadium',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => Stadium::class,
                    'property'       => 'label'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => [],

                            // Use key 'sort' if using ODM
                            'sort'  => [],
                        ],
                    ],
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select stadium',
                    'label'              => 'Stadium',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'host',
                'type' => 'Select',
                'options' => [
                    'label' => 'Host club',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'guest',
                'type' => 'Select',
                'options' => [
                    'label' => 'Guest club',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function setData($data)
    {
        if (isset($data['tournament']) && $data['tournament']) {
            /**
             * @var Tournament $tournament
             */
            $tournament = $this->getObjectManager()->getRepository(Tournament::class)
                ->find($data['tournament']);

            $this->get('season')
                ->setValueOptions($this->collectionToSelectOptions($tournament->getSeasons()));
        }

        if (isset($data['season']) && $data['season']) {
            /**
             * @var Season $season
             */
            $season = $this->getObjectManager()->getRepository(Season::class)
                ->find($data['season']);

            if ($season instanceof Season) {
                if ($season->getType() == Season::TYPE_LEAGUE) {
                    $this->getInputFilter()->get('tour')->setRequired(true);
                    $this->get('tour')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()->first()->getTours()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_CUP) {
                    $this->getInputFilter()->get('subStage')->setRequired(true);
                    $this->getInputFilter()->get('series')->setRequired(true);
                    $this->get('subStage')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()->first()->getSubStages()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_CHAMPIONSHIP) {
                    $this->getInputFilter()->get('stage')->setRequired(true);
                    $this->getInputFilter()->get('subStage')->setRequired(true);

                    $this->get('stage')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_EXHIBITION) {
                    $clubs = [];

                    foreach ($this->getObjectManager()->getRepository(Club::class)->findAll() as $club) {
                        $clubs[$club->getId()] = $club->getName($this->getOption('locale'));
                    }

                    $this->get('host')->setValueOptions(
                        $clubs
                    );
                    $this->get('guest')->setValueOptions(
                        $clubs
                    );
                }
            }
        }

        if (isset($data['stage']) && $data['stage']) {
            /**
             * @var Stage $stage
             */
            $stage = $this->getObjectManager()->getRepository(Stage::class)
                ->find($data['stage']);

            if ($stage->getType() == Stage::TYPE_SINGLE) {
                $this->get('tour')
                    ->setValueOptions($this->collectionToSelectOptions($stage->getTours()));
            } else {
                $this->get('subStage')
                    ->setValueOptions($this->collectionToSelectOptions($stage->getSubStages()));
            }
        }

        if (isset($data['subStage']) && $data['subStage']) {
            /**
             * @var Stage $subStage
             */
            $subStage = $this->getObjectManager()->getRepository(Stage::class)
                ->find($data['subStage']);

            if ($subStage->getType() == Stage::TYPE_PLAY_OFF_SUB_STAGE) {
                $this->getInputFilter()->get('series')->setRequired(true);
                $this->get('series')
                    ->setValueOptions($this->collectionToSelectOptions($subStage->getSeries()));
            } elseif ($subStage->getType() == Stage::TYPE_SUB_STAGE) {
                $this->getInputFilter()->get('tour')->setRequired(true);
                $this->get('tour')
                    ->setValueOptions($this->collectionToSelectOptions($subStage->getTours()));
            }
        }

        if (isset($data['series']) && $data['series']) {
            /**
             * @var Series $series
             */
            $series = $this->getObjectManager()->getRepository(Series::class)
                ->find($data['series']);

            if ($series->getFirst() && $series->getSecond()) {
                $this->get('host')
                    ->setValueOptions(
                        [
                            $series->getFirst()->getId()  => $series->getFirst()->getName($this->getOption('locale')),
                            $series->getSecond()->getId() => $series->getSecond()->getName($this->getOption('locale')),
                        ]
                    );
            } else {
                $clubs = [];

                foreach ($series->getStage()->getParent()->getSeason()->getClubs() as $club) {
                    $clubs[$club->getId()] = $club->getName($this->locale()->current());
                }

                $this->get('host')->setValueOptions($clubs);
                $this->get('guest')->setValueOptions($clubs);
            }
        }

        if (isset($data['tour']) && $data['tour']) {
            /**
             * @var Tour $tour
             */
            $tour = $this->getObjectManager()->getRepository(Tour::class)
                ->find($data['tour']);
            $stage = $tour->getStage();

            if ($stage->getType() == Stage::TYPE_SUB_STAGE) {
                $stage = $stage->getParent();
            }

            $clubs = [];

            foreach ($stage->getSeason()->getClubs() as $club) {
                $clubs[$club->getId()] = $club->getName($this->getOption('locale'));
            }

            $this->get('host')->setValueOptions($clubs);
            $this->get('guest')->setValueOptions($clubs);
        }

        return parent::setData($data);
    }

    /**
     * @inheritDoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        if (isset($data['tournament']) && $data['tournament']) {
            /**
             * @var Tournament $tournament
             */
            $tournament = $this->getObjectManager()->getRepository(Tournament::class)
                ->find($data['tournament']);

            if ($tournament) {
                $data['tournament'] = $tournament;
            }
        }

        if (isset($data['season']) && $data['season']) {
            /**
             * @var Season $season
             */
            $season = $this->getObjectManager()->getRepository(Season::class)
                ->find($data['season']);

            if ($season) {
                $data['season'] = $season;
            }
        }

        if (isset($data['stage']) && $data['stage']) {
            /**
             * @var Stage $stage
             */
            $stage = $this->getObjectManager()->getRepository(Stage::class)
                ->find($data['stage']);

            if ($stage) {
                $data['stage'] = $stage;
            }
        }

        if (isset($data['subStage']) && $data['subStage']) {
            /**
             * @var Stage $subStage
             */
            $subStage = $this->getObjectManager()->getRepository(Stage::class)
                ->find($data['subStage']);

            if ($subStage) {
                $data['subStage'] = $subStage;
            }
        }

        if (isset($data['tour']) && $data['tour']) {
            /**
             * @var Tour $tour
             */
            $tour = $this->getObjectManager()->getRepository(Tour::class)
                ->find($data['tour']);

            if ($tour) {
                $data['tour'] = $tour;
            }
        }

        if (isset($data['series']) && $data['series']) {
            /**
             * @var Series $series
             */
            $series = $this->getObjectManager()->getRepository(Series::class)
                ->find($data['series']);

            if ($series) {
                $data['series'] = $series;
            }
        }

        if (isset($data['stadium']) && $data['stadium']) {
            /**
             * @var Stadium $stadium
             */
            $stadium = $this->getObjectManager()->getRepository(Stadium::class)
                ->find($data['stadium']);

            if ($stadium) {
                $data['stadium'] = $stadium;
            }
        }

        if (isset($data['host']) && $data['host']) {
            /**
             * @var Club $host
             */
            $host = $this->getObjectManager()->getRepository(Club::class)
                ->find($data['host']);

            if ($host) {
                $data['host'] = $host;
            }
        }

        if (isset($data['guest']) && $data['guest']) {
            /**
             * @var Club $guest
             */
            $guest = $this->getObjectManager()->getRepository(Club::class)
                ->find($data['guest']);

            if ($guest) {
                $data['guest'] = $guest;
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
            'date'    => [
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
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y',
                        ],
                    ],
                ],
            ],
            'status' => [
                'required' => true,
            ],
            'time' => [
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
                        'name' => 'Date',
                        'options' => [
                            'format' => 'H:i',
                        ],
                    ],
                ],
            ],
            'stadium' => [
                'required' => true,
            ],
            'host' => [
                'required' => true,
            ],
            'guest' => [
                'required' => true,
            ],
            'tournament' => [
                'required' => true,
            ],
            'season' => [
                'required' => true,
            ],
            'stage' => [
                'required' => false,
            ],
            'subStage' => [
                'required' => false,
            ],
            'tour'   => [
                'required' => false,
            ],
            'series' => [
                'required' => false,
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
     * @param Collection $collection
     * @return array
     */
    private function collectionToSelectOptions(Collection $collection)
    {
        $result = [];

        foreach ($collection as $item) {
            $result[$item->getId()] = [
                'label' => $item->getLabel($this->getOption('locale')),
                'attributes' => [
                    'data-type' => (method_exists($item, 'getType') ? $item->getType() : '')
                ],
                'value' => $item->getId(),
            ];
        }

        return $result;
    }
}