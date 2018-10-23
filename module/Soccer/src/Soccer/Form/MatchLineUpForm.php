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
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;
use Soccer\Entity\Club;
use Soccer\Entity\ClubPlayer;
use Soccer\Entity\LineUp;
use Soccer\Entity\Match;
use Soccer\Repository\ClubPlayersRepository;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * MatchLineUpForm
 */
class MatchLineUpForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Match
     */
    protected $match;

    /**
     * @inheritDoc
     */
    public function __construct($name = 'match-line-up-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(
            [
                'name' => 'hostStarters',
                'type' => ObjectSelect::class,
                'options' => [
                    'label'          => 'Starting',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => ClubPlayer::class,
                    'property'       => 'name'.$this->getOption('locale'),
                    'optgroup_identifier' => 'positionLabel'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => ['club' => $this->getMatch()->getHost()],
                            'orderBy'  => [],
                            'sort'     => [],
                        ],
                    ],
                ],
                'attributes' => [
                    'multiple' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'hostSubstitutes',
                'type' => ObjectSelect::class,
                'options' => [
                    'label'          => 'Substitutes',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => ClubPlayer::class,
                    'property'       => 'name'.$this->getOption('locale'),
                    'optgroup_identifier' => 'positionLabel'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => ['club' => $this->getMatch()->getHost()],
                            'orderBy'  => [],
                            'sort'     => [],
                        ],
                    ],
                ],
                'attributes' => [
                    'multiple' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'guestStarters',
                'type' => ObjectSelect::class,
                'options' => [
                    'label'          => 'Starting',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => ClubPlayer::class,
                    'property'       => 'name'.$this->getOption('locale'),
                    'optgroup_identifier' => 'positionLabel'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => ['club' => $this->getMatch()->getGuest()],
                            'orderBy'  => [],
                            'sort'     => [],
                        ],
                    ],
                ],
                'attributes' => [
                    'multiple' => true,
                ],
            ]
        );

        $this->add(
            [
                'name' => 'guestSubstitutes',
                'type' => ObjectSelect::class,
                'options' => [
                    'label'          => 'Substitutes',
                    'object_manager' => $this->getObjectManager(),
                    'target_class'   => ClubPlayer::class,
                    'property'       => 'name'.$this->getOption('locale'),
                    'optgroup_identifier' => 'positionLabel'.$this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => ['club' => $this->getMatch()->getGuest()],
                            'orderBy'  => [],
                            'sort'     => [],
                        ],
                    ],
                ],
                'attributes' => [
                    'multiple' => true,
                ],
            ]
        );

        $data = [];

        /**
         * @var LineUp $lineUp
         */
        foreach ($this->getMatch()->getLineUp() as $lineUp) {
            if ($lineUp->getClub()->getId() == $this->getMatch()->getHost()->getId()) {
                $keyPre = 'host';
            }

            if ($lineUp->getClub()->getId() == $this->getMatch()->getGuest()->getId()) {
                $keyPre = 'guest';
            }

            /**
             * @var ClubPlayer $starter
             */
            foreach ($lineUp->getStarters() as $starter) {
                $data[$keyPre . 'Starters'][] = $starter->getId();
            }

            /**
             * @var ClubPlayer $substitute
             */
            foreach ($lineUp->getSubstitutes() as $substitute) {
                $data[$keyPre . 'Substitutes'][] = $substitute->getId();
            }
        }

        $this->setData($data);
    }

    public function isValid()
    {
        $result = parent::isValid();
        $flag   = $result;

        if ($result == true) {
            $data = $this->getData();

            if (count($data['hostStarters']) !== 11) {
                $this->get('hostStarters')
                    ->setMessages(
                        [
                            'count' => 'Starting team must contain from 11 players',
                        ]
                    );

                $flag = false;
            }

            if (count($data['guestStarters']) !== 11) {
                $this->get('hostStarters')
                    ->setMessages(
                        [
                            'count' => 'Starting team must contain from 11 players',
                        ]
                    );

                $flag = false;
            }
        }

        return $flag;
    }

    /**
     * @inheritDoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);
        /**
         * @var ClubPlayersRepository $clubPlayersR
         */
        $clubPlayersR = $this->getObjectManager()->getRepository(ClubPlayer::class);

        if ($data['hostStarters']) {
            $hostStarters    = $clubPlayersR->findBy(
                [
                    'id' => $data['hostStarters']
                ]
            );

            $data['hostStarters'] = $hostStarters;
        }

        if ($data['hostSubstitutes']) {
            $hostSubstitutes = $clubPlayersR->findBy(
                [
                    'id' => $data['hostSubstitutes']
                ]
            );

            $data['hostSubstitutes'] = $hostSubstitutes;
        }

        if ($data['guestStarters']) {
            $guestStarters = $clubPlayersR->findBy(
                [
                    'id' => $data['guestStarters']
                ]
            );

            $data['guestStarters'] = $guestStarters;
        }

        if ($data['guestSubstitutes']) {
            $guestSubstitutes = $clubPlayersR->findBy(
                [
                    'id' => $data['guestSubstitutes']
                ]
            );

            $data['guestSubstitutes'] = $guestSubstitutes;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'hostStarters'      => [
                'required' => true,
            ],
            'hostSubstitutes'   => [
                'required' => true,
            ],
            'guestStarters'     => [
                'required' => true,
            ],
            'guestSubstitutes'  => [
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
        return $this;
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param Match $match
     * @return self
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;
        $this->init();
        return $this;
    }
}