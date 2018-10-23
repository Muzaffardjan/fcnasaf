<?php 
/**
 * TemplateMenu
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\ObjectManagerAwareInterface;

class TemplateMenu 
extends Fieldset 
implements ObjectManagerAwareInterface, InputFilterProviderInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $inputFilterSpecification;

    /**
     * {@inheritdoc}
     
    public function __construct($name, $label, $options = []) 
    {
        parent::__construct($name, $options);
    }*/

    /**
     * Initiates fieldset
     */
    public function init()
    {   
        $name    = $this->getName();
        $locales = $this->getOption('locales');

        $this->add(
            [
                'name' => 'default',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'label'              => 'Default',
                    'object_manager'     => $this->getObjectManager(),
                    'target_class'       => 'Menu\Entity\Container',
                    'property'           => 'label',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Not specified',
                ],
            ]
        );

        if (!$locales) {
            $this->inputFilterSpecification['default'] = [
                'required' => true,
            ];
        } else {
            $this->inputFilterSpecification['default'] = [
                'required' => false,
            ];

            foreach ($locales as $locale => $lang) {
                $this->add(
                    [
                        'name' => $locale,
                        'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                        'options' => [
                            'label'              => $lang,
                            'object_manager'     => $this->getObjectManager(),
                            'target_class'       => 'Menu\Entity\Container',
                            'property'           => 'label',
                            'display_empty_item' => true,
                            'empty_item_label'   => 'Not specified',
                        ],
                    ]
                );

                $this->inputFilterSpecification[$locale] = [
                    'required' => false,
                ];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpecification;
    }

    /**
     * Gets doctrine object manager
     * 
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Sets doctrine obeject manager and inits form
     * 
     * @param   ObjectManager $objectManager
     * @return  self
     */
    public function setObjectManager(ObjectManager $objectManager) 
    {
        $this->objectManager = $objectManager;

        // init fieldset here
        // because form can not initiate without object manager
        $this->init();

        return $this;
    }
}