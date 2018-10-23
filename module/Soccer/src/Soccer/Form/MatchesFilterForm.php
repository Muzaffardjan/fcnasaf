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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Match;
use Soccer\Entity\Season;
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Entity\Tour;
use Soccer\Entity\Tournament;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * MatchesFilterForm
 */
class MatchesFilterForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'matches-filter-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->setAttribute('method', 'GET');
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
                    'label' => 'Tournament',
                    'empty_item_label'   => '-- Tournament --',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'season',
                'type' => 'Select',
                'options' => [
                    'empty_option' => '-- Season --',
                    'label' => 'Season',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'stage',
                'type' => 'Select',
                'options' => [
                    'empty_option' => '-- Stage --',
                    'label' => 'Stage',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'subStage',
                'type' => 'Select',
                'options' => [
                    'empty_option' => '-- Sub stage --',
                    'label' => 'Sub stage',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'tour',
                'type' => 'Select',
                'options' => [
                    'empty_option' => '-- Tour --',
                    'label' => 'Tour',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'series',
                'type' => 'Select',
                'options' => [
                    'empty_option' => '-- Series --',
                    'label' => 'Series',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'status',
                'type' => 'Select',
                'options' => [
                    'label' => 'Status',
                    'value_options' => [
                        Match::STATUS_QUEUE     => 'Queue',
                        Match::STATUS_ONGOING   => 'Ongoing',
                        Match::STATUS_FINISHED  => 'Finished',
                    ],
                    'empty_option' => 'All',
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

        return $data;
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
                    $this->get('tour')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()->first()->getTours()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_CUP) {
                    $this->get('subStage')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()->first()->getSubStages()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_CHAMPIONSHIP) {
                    $this->get('stage')
                        ->setValueOptions(
                            $this->collectionToSelectOptions(
                                $season->getStages()
                            )
                        );
                } elseif ($season->getType() == Season::TYPE_EXHIBITION) {
                    // do nothing default is enough
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
                $this->get('series')
                    ->setValueOptions($this->collectionToSelectOptions($subStage->getSeries()));
            } elseif ($subStage->getType() == Stage::TYPE_SUB_STAGE) {
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
        }

        if (isset($data['tour']) && $data['tour']) {
            /**
             * @var Tour $tour
             */
            $tour = $this->getObjectManager()->getRepository(Tour::class)
                ->find($data['tour']);
        }

        return parent::setData($data);
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
        $this->init();
        return $this;
    }

    public function getInputFilterSpecification()
    {
        return [
            'tournament' => [
                'required' => false,
            ],
            'season' => [
                'required' => false,
            ],
            'stage' => [
                'required' => false,
            ],
            'subStage' => [
                'required' => false,
            ],
            'tour' => [
                'required' => false,
            ],
            'series' => [
                'required' => false,
            ],
            'status' => [
                'required' => false,
            ],
        ];
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