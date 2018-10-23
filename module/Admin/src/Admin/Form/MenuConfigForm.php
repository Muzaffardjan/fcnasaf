<?php 
/**
 * MenuConfigForm
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\Form;

use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;
use Application\ObjectManagerAwareInterface;
use Admin\WebsiteConfig\TemplatePositionsConfig;
use Admin\Form\Fieldset\TemplateMenu as TemplateMenuFieldset;

class MenuConfigForm 
extends Form
implements ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var TemplatePositionsConfig
     */
    protected $configTemplatePostions;

    /**
     * @var array
     */
    protected $locales;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'menu-config-form', $options = [])
    {
        parent::__construct($name, $options);
    }

    /**
     * Adds form elements
     * 
     * @return self
     */
    public function init()
    {
        $navs    = $this->getTemplatePositionsConfig()->get('navigations');
        $locales = $this->getLocales();
        $data    = [];

        foreach ($navs as $name => $nav) {
            $field = new TemplateMenuFieldset(
                $name,
                [
                    'label'   => $nav->offsetGet('title'),
                    'locales' => $this->getLocales(),
                ]
            );

            $this->add(
                $field->setObjectManager($this->getObjectManager())
            );

            $data[$name] = $nav['menu']->toArray();
        }

        $this->setData($data);
    }

    /**
     * Gets doctrine object manager
     * 
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Sets doctrine object manager and initiates form
     * 
     * @param   ObjectManager $objectManager
     * @return  self
     */
    public function setObjectManager(ObjectManager $objectManager) 
    {
        $this->objectManager = $objectManager;

        // init form here
        // because form can not initiate without object manager
        $this->init();

        return $this;
    }

    /**
     * Gets template postions config
     * @return TemplatePositionsConfig
     * @throws \Exception
     */
    public function getTemplatePositionsConfig()
    {
        if (null === $this->configTemplatePostions) {
            throw new \Exception(
                "Website template options config is undefined"
            );
        }

        return $this->configTemplatePostions;
    }

    /**
     * Sets template postions config
     * 
     * @param   TemplatePositionsConfig $configTemplatePostions Website template postions config
     * @return  self
     */
    public function setTemplatePositionsConfig(
        TemplatePositionsConfig $configTemplatePostions
    ) {
        $this->configTemplatePostions = $configTemplatePostions;

        return $this;
    }

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
}