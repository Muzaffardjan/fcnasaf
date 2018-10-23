<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form;

use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Soccer\Entity\Club;
use Soccer\Form\Fieldset\LocalesTextFieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * GroupPlayForm
 */
class GroupPlayForm extends Form
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * GroupPlayForm constructor.
     *
     * @param string $name
     * @param array  $options
     */
    public function __construct($name = 'group-play-form', array $options = [])
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