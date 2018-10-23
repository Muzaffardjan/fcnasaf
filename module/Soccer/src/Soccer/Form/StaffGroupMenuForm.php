<?php
/**
 * StaffGroupMenuForm
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class StaffGroupMenuForm extends Form implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct($name = 'staff-group-menu-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Initiates form elements
     * @return void
     */
    public function init()
    {
        $this->add(
            [
                'name' => 'group',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => 'Soccer\Entity\StaffGroup',
                    'property'        => 'name',
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
                    'label'              => 'Group',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select group',
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
        $data = parent::getData($flag);

        if (isset($data['group'])) {
            $data['group'] = $this->getObjectManager()
                ->getRepository(
                    $this->get('group')
                        ->getOption('target_class')
                )
                ->find($data['group']);
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
     * @return StaffGroupMenuForm
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
            'group' => [
                'required' => false,
            ],
        ];
    }
}