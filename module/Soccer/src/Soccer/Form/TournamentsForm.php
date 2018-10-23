<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * TournamentsForm
 */
class TournamentsForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = 'tournaments-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            [
                'name'    => 'aliasName',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Alias name for accessability',
                ],
            ]
        );

        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'label' => 'Label',
                    'locales' => $this->getOption('locales')
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'aliasName' => [
                'required' => true,
            ],
        ];
    }
}