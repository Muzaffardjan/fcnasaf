<?php
/**
 * StaffGroupConfigForm
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\Form;

use Admin\Form\Fieldset\StaffGroupHomePage;
use Application\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class StaffGroupConfigForm
    extends Form
    implements InputFilterProviderInterface, ObjectManagerAwareInterface
{
    #region ObjectManagerAwareInterface
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * @inheritDoc
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->init();
        return $this;
    }
    #endregion

    /**
     * Gets locales
     *
     * @return array Locales used for elements
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Sets locales
     *
     * @param   array $locales
     * @return  self
     */
    public function setLocales(array $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __construct($name = 'staff-group-config-form', array $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Adds form elements
     */
    public function init()
    {
        $homePageField = new StaffGroupHomePage(
            'home_page',
            [
                'label'   => 'Staff group cards at homepage',
                'locales' => $this->getLocales(),
            ]
        );

        $homePageField->setObjectManager($this->getObjectManager());

        $this->add($homePageField);
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [];
    }
}