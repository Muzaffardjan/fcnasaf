<?php
/**
 * FC Nasaf official website
 *
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.each.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Soccer\Form;

use Zend\InputFilter\InputFilterProviderInterface;

class CommentMatchEventForm extends AbstractMatchEventForm implements InputFilterProviderInterface
{
    public function __construct($name = 'comment-match-event-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        parent::init();

        $this->add(
            [
                'name' => 'locale',
                'type' => 'Select',
                'options' => [
                    'value_options' => $this->getOption('locales'),
                    'label'         => 'Language',
                ],
            ]
        );

        if ($this->getOption('locale')) {
            $this->get('locale')->setValue($this->getOption('locale'));
        }

        $this->add(
            [
                'name' => 'text',
                'type' => 'Text',
                'options' => [
                    'label' => 'Comment',
                ],
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'locale' => [
                'required' => true,
            ],
            'text'   => [
                'required' => true,
            ],
        ];
    }
}