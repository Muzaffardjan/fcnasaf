<?php 
/**
 * Configuration form
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use ImagesManager\Watermarker;

class Configuration extends Form implements InputFilterProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        $name    = 'images-manager-config-form', 
        $options = null
    ) {
        parent::__construct($name, $options);

        /**
         * Watermark toggle
         */
        $this->add(
            [
                'name'      => 'watermark_use',
                'type'      => 'Checkbox',
                'options'   => [
                    'label'              => 'Watermark',
                    'use_hidden_element' => false,
                ], 
            ]
        );

        /**
         * Watermark image itself
         */
        $this->add(
            [
                'name'     => 'watermark',
                'type'     => 'File',
                'options'  => [
                    'label' => 'Watermark upload',
                ], 
            ]
        );

        /**
         * User can assign static watermark ratio to image
         */
        $this->add(
            [
                'name'     => 'scale',
                'type'     => 'Text',
                'options'  => [
                    'label' => 
                    'Scale of the watermark relative to the image (in %)',
                ],
            ]
        );

        /**
         * Watermark position
         */
        $this->add(
            [
                'name'     => 'x_position',
                'type'     => 'Select',
                'options'  => [
                    'label'         => 'X position',
                    'value_options' => array(
                        Watermarker::POSITION_LEFT   => 'Left',
                        Watermarker::POSITION_CENTER => 'Center',
                        Watermarker::POSITION_RIGHT  => 'Right',
                    ),
                ],
            ]
        );

        $this->add(
            [
                'name'     => 'y_position',
                'type'     => 'Select',
                'options'  => [
                    'label' => 'Y position',
                    'value_options' => array(
                        Watermarker::POSITION_TOP    => 'Top',
                        Watermarker::POSITION_CENTER => 'Center',
                        Watermarker::POSITION_BOTTOM => 'Bottom',
                    ),
                ],
            ]
        );

        // Default values
        $this->get('x_position')->setValue(Watermarker::POSITION_CENTER);
        $this->get('y_position')->setValue(Watermarker::POSITION_CENTER);
        $this->get('scale')->setValue(100);
    }

    public function getInputFilterSpecification()
    {
        return [
            'watermark_use' => [
                'required' => false,
            ],
            'watermark' => [
                'required'   => false,
                'validators' => [
                    [
                        'name'     => 'File\Extension',
                        'options'  => [
                            'extension' => ['png'],
                        ], 
                    ],
                    [
                        'name' => 'File\IsImage',
                    ],
                ],
                'filters'   => [
                    [
                        'name'    => 'File\RenameUpload',
                        'options' => [
                            'target'    => $this->getOption('watermark_path'),
                            'overwrite' => true,
                        ], 
                    ],
                ],
            ],
            'scale' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Digits',
                    ],
                    [
                        'name' => 'Between',
                        'options' => [
                            'inclusive' => true,
                            'min'       => 1,
                            'max'       => 100, 
                        ],
                    ],
                ],                
            ],
            'x_position' => [
                'required' => false,
            ],
            'y_position' => [
                'required' => false,
            ],
        ];
    }
}