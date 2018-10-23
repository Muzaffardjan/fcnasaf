<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Admin\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Stage;
use Zend\Form\Form;

/**
 * HomeSoccerCupForm
 */
class HomeSoccerCupForm extends Form implements ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'home-soccer-cup-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(
            [
                'name' => 'cup',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => Stage::class,
                    'property'        => 'label' . $this->getOption('locale'),
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [
                                'type' => Stage::TYPE_PLAY_OFF
                            ],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['season' => 'DESC'],

                            // Use key 'sort' if using ODM
                            'sort'  => []
                        ],
                    ],
                    'label'              => 'Display diagram of',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select',
                ],
            ]
        );
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
}