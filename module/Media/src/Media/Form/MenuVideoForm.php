<?php 
/**
 * Menu element form
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Application\ObjectManagerAwareTrait;

class MenuVideoForm extends Form implements InputFilterProviderInterface
{
    use ObjectManagerAwareTrait;

    public function __construct(ObjectManager $objectManager, $name = 'menu-article-form', $options = [])
    {
        $this->setObjectManager($objectManager);
        parent::__construct($name, $options);

        $this->add(
            [
                'name' => 'selected',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'   => [
                    'object_manager'  => $this->getObjectManager(),
                    'target_class'    => 'Media\Entity\VideoInfo',
                    'property'        => 'title',
                    'optgroup_identifier' => 'locale',
                    'is_method'      => true,
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => [],

                            // Use key 'orderBy' if using ORM
                            'orderBy'  => ['id' => 'DESC'],

                            // Use key 'sort' if using ODM
                            'sort'  => []
                        ],
                    ],
                    'label'           => 'Select video that you want to add to menu',
                    'display_empty_item' => true,
                    'empty_item_label'   => 'Select video',
                ],
                'attributes' => [
                    'multiple' => true
                ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'selected' => [
                'required' => true,
            ]
        ];
    }
}