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


use Zend\Form\Form;

abstract class AbstractMatchEventForm extends Form
{
    public function init()
    {
        $this->add(
            [
                'name'    => 'minutes',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Minutes',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'seconds',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Seconds',
                ],
            ]
        );

        $this->add(
            [
                'name'    => 'extra',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Extra',
                ],
            ]
        );
    }
}