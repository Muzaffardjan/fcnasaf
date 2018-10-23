<?php 
/**
 * Articles form
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Articles\Entity\Article;

class ArticlesForm 
extends Form 
implements InputFilterProviderInterface
{    
    /**
     * @var int
     */
    protected $mode;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'articles-form', $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'locale',
                'type'    => 'Select',
                'options' => [
                    'label'         => 'Language',
                    'empty_option'  => 'Select language',
                ],
            ]
        );

        $this->get('locale')
        ->setValueOptions($this->getOption('locales'))
        ->setValue($this->getOption('locale'));

        $this->add(
            [
                'name'    => 'title',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Title',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'image',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Title image',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'body',
                'type'    => 'Textarea',
                'options' => [
                    'label' => 'Body',
                ],
            ]
        );

        /*$this->add(
            [
                'name'    => 'category',
                'type'    => 'Select',
                'options' => [
                    'label' => 'Category',                    
                    'empty_option' => 'Select category',
                ],
            ]
        );*/

        $this->add(
            [
                'name' => 'category',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'label'          => 'Category',
                    'empty_item_label' => 'Select category',
                    'display_empty_item' => true,
                    'object_manager' => $this->getOption('entity_manager'),
                    'target_class'   => '\Articles\Entity\Category',
                    'property'       => 'title',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'getByLocale',
                        'params' => [
                            'locale' => $this->getOption('locale'),
                        ],
                    ],
                ],  
            ]
        );

        $this->add(
            [
                'name'    => 'publish',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Publish at',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'hidden',
                'type'    => 'Checkbox',
                'options' => [
                    'label' => 'Hidden',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'publish_date',
                'type'    => 'Text',
            ]
        );

        $this->add(
            [
                'name'    => 'publish_time',
                'type'    => 'Text',
            ]
        );

        $this->add(
            [
                'name'    => 'save',
                'type'    => 'Submit',
                'options' => [
                    'label' => 'Save',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'draft',
                'type'    => 'Submit',
                'options' => [
                    'label' => 'Save as draft',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $valueOptions = $this->get('locale')->getValueOptions();
        $value        = $this->get('locale')->getValue();

        if (isset($valueOptions[$value])) {
            $this->get('category')->setOption(
                'find_method', 
                [
                    'name'   => 'getByLocale',
                    'params' => [
                        'locale' => $value,
                    ],
                ]
            );
        }

        return parent::isValid();
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        $flag = ($this->getMode() === Article::STATUS_DRAFT);

        return [
            'locale' => [
                'required' => true,
            ],
            'title' => [
                'required' => !$flag,
                'filter'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
            'image' => [
                'required' => false,
                'filter'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
            'body' => [
                'required' => false,
                'filter'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
            ],
            'category' => [
                'required' => false,
            ],
            'hidden' => [
                'required' => false,
            ],
            'publish_date' => [
                'required' => false,
                'filter'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
            'publish_time' => [
                'required' => false,
                'filter'   => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
            'save' => [
                'required' => false,
            ],
            'draft' => [
                'required' => false,
            ],
        ];
    }

    /**
     * Gets the value of mode.
     *
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Sets the value of mode.
     *
     * @param int $mode the mode
     *
     * @return self
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }
}