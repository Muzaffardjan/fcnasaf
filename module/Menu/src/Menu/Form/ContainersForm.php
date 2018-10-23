<?php 
/**
 * ContainersForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ContainersForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'container-form', $options = null)
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'      => 'label',
                'type'      => 'Text',
                'options'   => [
                    'label'  => 'Label',
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'label' => [
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                ],
            ],
        ];
    }
}