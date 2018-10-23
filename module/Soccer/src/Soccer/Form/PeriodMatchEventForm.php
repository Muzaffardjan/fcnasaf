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

use Soccer\Entity\MatchPeriod;
use Zend\InputFilter\InputFilterProviderInterface;

class PeriodMatchEventForm extends AbstractMatchEventForm implements InputFilterProviderInterface
{
    public function __construct($name = 'period-match-event-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        parent::init();

        $this->add(
            [
                'name' => 'type',
                'type' => 'Select',
                'options' => [
                    'label' => 'Period',
                    'value_options' => [
                        MatchPeriod::TYPE_START         => 'Match started',
                        MatchPeriod::TYPE_HALF_TIME     => 'Half time',
                        MatchPeriod::TYPE_MAIN_TIME_END => 'End of main time',
                        MatchPeriod::TYPE_FINISH        => 'Match ended',
                    ],
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
            'type' => [
                'required' => true,
            ],
        ];
    }
}