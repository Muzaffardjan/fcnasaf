<?php 
/**
 * VideosForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class VideosForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'videos-form', $options = null) 
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'date',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Date',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'poster',
                'type'    => 'Textarea',
                'options' => [
                    'label' => 'Poster',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'source',
                'type'    => 'Textarea',
                'options' => [
                    'label' => 'Source',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'date' => [
                'filters' => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'd.m.Y',
                        ],
                    ],
                ],
            ],
            'poster' => [
                'filters' => [
                    [
                        'name'    => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Uri',
                    ],
                ],
            ],
            'source' => [
                'filters' => [
                    [
                        'name'    => 'StripTags',
                        'options' => [
                            'allowTags' => [
                                'iframe',
                                'object',
                                'video',
                            ],
                            'allowAttribs' => [
                                'frameborder',
                                'height',
                                'name',
                                'sandbox',
                                'src',
                                'width',
                            ],
                        ],
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ],
        ];
    }
}