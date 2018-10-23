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
use Soccer\Entity\Club;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * StadiumsForm
 */
class StadiumsForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'stadiums-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(
            new LocalesTextFieldset(
                'name',
                [
                    'locales' => $this->getOption('locales'),
                    'label'   => 'Name',
                ]
            )
        );

        $this->add(
            new LocalesTextFieldset(
                'located',
                [
                    'locales' => $this->getOption('locales'),
                    'label'   => 'Located',
                ]
            )
        );

        $this->add(
            [
                'name' => 'owner',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => Club::class,
                    'property'        => 'name' . $this->getOption('locale'),
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
                    'label'              => 'Owner',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select owner',
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

        if (isset($data['owner']) && $data['owner']) {
            $clubsR = $this->getObjectManager()->getRepository(Club::class);
            $club = $clubsR->find($data['owner']);

            if ($club) {
                $data['owner'] = $club;
            }
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
            'owner' => [
                'required' => false,
            ],
        ];
    }
}