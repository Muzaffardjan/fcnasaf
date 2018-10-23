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
 * ToursForm
 */
class ToursForm extends Form
{
    /**
     * @inheritDoc
     */
    public function __construct($name = 'tours-form', array $options = [])
    {
        parent::__construct($name, $options);

        $this->add(
            new LocalesTextFieldset(
                'label',
                [
                    'locales' => $this->getOption('locales'),
                    'label'   => 'Label',
                ]
            )
        );
    }
}