<?php 
/**
 * Categories form
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class CategoriesForm extends Form implements InputFilterProviderInterface 
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'categories-form', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'       => 'title',
                'type'       => 'Text',
                'options'    => [
                    'label' => 'Title',
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'locale',
                'type'       => 'Select',
                'options'    => [
                    'label'         => 'Language',
                    'empty_option'  => 'Select language', 
                ],
                'attributes' => [
                    'required' => true,
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            'title' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ],
            'locale' => [
                'required' => true,
            ],
        ]; 
    }
}