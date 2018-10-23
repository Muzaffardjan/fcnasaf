<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Admin\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Club;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * PlayerCardsConfigForm
 */
class PlayerCardsConfigForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'player-cards-config-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
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
                    'label'              => 'Display players of',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'club' => [
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
}