<?php
/**
 * TemplateBlock
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\View\Helper;

use Admin\WebsiteConfig\TemplatePositionsConfig;
use Application\ObjectManagerAwareInterface;
use Application\ObjectManagerAwareTrait;
use Zend\Config\Config;
use Zend\Filter\FilterChain;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\CamelCaseToDash;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Stdlib\Exception\RuntimeException;

class TemplateBlock extends AbstractHelper implements ObjectManagerAwareInterface
{
    #region initial
    use ObjectManagerAwareTrait;

    /**
     * @var array
     */
    protected $blocksDefaultOptions = [
        'multiple'       => false,
    ];

    /**
     * @var TemplatePositionsConfig
     */
    protected $configTemplatePositions;

    /**
     * @var array|Config
     */
    protected $configApplication;

    /**
     * @var string
     */
    protected $partial;

    /**
     * @var array Blocks cache
     */
    protected $blocks;

    /**
     * @var array Block options cache
     */
    protected $options;

    /**
     * @var FilterChain
     */
    protected $nameFilterChain;

    /**
     * TemplateBlock constructor.
     * @param array|Config $configApplication
     * @param TemplatePositionsConfig $configTemplatePositions
     */
    public function __construct($configApplication, TemplatePositionsConfig $configTemplatePositions)
    {
        $this->configApplication = $configApplication;
        $this->configTemplatePositions = $configTemplatePositions;
    }

    /**
     * @param   string        $block
     * @param   null|string   $partial
     * @return  mixed
     */
    public function __invoke($block, $partial = null)
    {
        if (null !== $partial) {
            $this->setPartial($partial);
        }

        return $this->get($block);
    }

    /**
     * @return array|Config
     */
    public function getApplicationConfig()
    {
        return $this->configApplication;
    }

    /**
     * @param array|Config $configApplication
     * @return TemplateBlock
     */
    public function setApplicationConfig($configApplication)
    {
        $this->configApplication = $configApplication;
        return $this;
    }

    /**
     * @return TemplatePositionsConfig
     */
    public function getTemplatePositionsConfig()
    {
        return $this->configTemplatePositions;
    }

    /**
     * @param TemplatePositionsConfig $configTemplatePositions
     * @return TemplateBlock
     */
    public function setTemplatePositionsConfig($configTemplatePositions)
    {
        $this->configTemplatePositions = $configTemplatePositions;
        return $this;
    }

    /**
     * @return string
     */
    public function getPartial()
    {
        if (null === $this->partial) {
            throw new RuntimeException(
                'Partial required'
            );
        }

        return $this->partial;
    }

    /**
     * @param string $partial
     * @return TemplateBlock
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * @return FilterChain
     */
    public function getNameFilterChain()
    {
        if (null === $this->nameFilterChain) {
            $this->nameFilterChain = new FilterChain();

            $this->nameFilterChain->attach(new CamelCaseToUnderscore())
                ->attach(new StringToLower());
        }

        return $this->nameFilterChain;
    }

    /**
     * @param FilterChain $nameFilterChain
     * @return TemplateBlock
     */
    public function setNameFilterChain(FilterChain $nameFilterChain)
    {
        $this->nameFilterChain = $nameFilterChain;
        return $this;
    }
    #endregion

    /**
     * @param   string $name
     * @return  array
     */
    public function getBlockOptions($name)
    {
        if (!isset($this->options[$name])) {
            $separated = explode('\\', $this->getNameFilterChain()->filter($name));
            $options   = $this->getTemplatePositionsConfig()->get()->toArray();

            foreach ($separated as $depthKey) {
                if (isset($options[$depthKey]) && is_array($options[$depthKey])) {
                    $options = $options[$depthKey];
                } else {
                    throw new RuntimeException(
                        "Undefined or invalid template block config"
                    );
                }
            }

            $this->options[$name] = array_merge(
                $this->blocksDefaultOptions,
                $options
            );
        }

        return $this->options[$name];
    }

    /**
     * @param   string  $name
     * @return  mixed
     */
    public function get($name)
    {
        $locale = $this->getView()->locale()->current();
        
        if (!isset($this->blocks[$name][$locale][$this->getPartial()]))
        {
            $options = $this->getBlockOptions($name);
            
            if (!isset($options['target_entity'])) {
                throw new RuntimeException(
                    "Invalid block config, undefined target entity"
                );
            }
            
            $repo = $this->getObjectManager()
                ->getRepository($options['target_entity']);

            if  ($options['multiple'] == true) {
                $block = $repo->findBy(['id' => $options[$locale]]);
            } else {
                $block = $repo->findOneBy(['id' => $options[$locale]]);
            }

            $this->blocks[$name][$locale][$this->getPartial()] = $this->getView()->partial(
                $this->getPartial(),
                ['container' => $block]
            );
        }
        
        return $this->blocks[$name][$locale][$this->getPartial()];
    }
}