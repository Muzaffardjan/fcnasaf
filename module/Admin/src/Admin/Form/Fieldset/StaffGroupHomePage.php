<?php
/**
 * StaffGroupHomePage
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Exception\RuntimeException;

class StaffGroupHomePage extends Fieldset implements  InputFilterProviderInterface
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
     * Initiates fieldset
     */
    public function init()
    {
        $locales = $this->getOption('locales');

        if (!$locales) {
            throw new RuntimeException("Locales array expected.");
        }

        foreach ($locales as $locale => $lang) {
            $this->add(
                [
                    'name' => $locale,
                    'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                    'options' => [
                        'label'              => $lang,
                        'object_manager'     => $this->getObjectManager(),
                        'target_class'       => 'Soccer\Entity\StaffGroup',
                        'property'           => 'name',
                        'display_empty_item' => true,
                        'empty_item_label'   => 'Not specified',
                    ],
                    'attributes' => [
                        'multiple' => true,
                    ],
                ]
            );

            $this->inputFilterSpecification[$locale] = [
                'required' => false,
            ];
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

        // init fielset here
        // because form can not initiate without object manager
        $this->init();

        return $this;
    }
}