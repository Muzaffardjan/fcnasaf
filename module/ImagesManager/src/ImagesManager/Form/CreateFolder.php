<?php 
/**
 * Create new folder form
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Regex;

class CreateFolder extends Form implements InputFilterProviderInterface 
{
    public function __construct($name = 'new-folder', $options = null) 
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'      => 'name',
                'type'      => 'Text',
                'options'   => [
                    'label' => 'Folder name',
                ],
            ]
        );
    } 

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required'      => true,
                'filters'       => [
                    [
                        'name' => 'StringTrim',
                    ],
                ],
                'validators'    => [
                    [
                        'name'      => 'StringLength',
                        'options'   => [
                            'max' => 255,
                        ], 
                    ],
                    [
                        'name'      => 'Regex',
                        'options'   => [
                            'pattern'   => '/^[a-zA-Z0-9\(\)_-]+$/',
                            'messages'  => [
                                Regex::NOT_MATCH => 'Folder name contains forbidden symbols',
                            ],
                        ], 
                    ],
                ],
            ],
        ];
    }
}