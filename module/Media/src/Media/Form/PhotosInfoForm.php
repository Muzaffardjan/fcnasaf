<?php 
/**
 * PhotosInfoForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class PhotosInfoForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'photos-info-form', $options = null) 
    {
        parent::__construct($name, $options);

        if ($this->getOption('use_locale') !== false) {
            $this->add(
                [
                    'name'    => 'locale',
                    'type'    => 'Select',
                    'options' => [
                        'label' => 'Language',
                        'empty_option' => 'Select language'
                    ],
                ]
            );
        }

        $this->add(
            [
                'name'    => 'title',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Title',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'locale' => [
                'required' => $this->getOption('use_locale') !== false,
            ],
            'title' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ],
        ];
    }
}