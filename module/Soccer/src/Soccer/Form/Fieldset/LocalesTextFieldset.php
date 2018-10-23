<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Form\Fieldset;

use Zend\Form\Exception\BadMethodCallException;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * LocalesTextFieldset
 */
class LocalesTextFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * @var array
     */
    protected $inputFilterSpecification;

    /**
     * @var array
     */
    protected $elementSpecification = [
        'required'   => true,
        'filters'    => [
            [
                'name' => 'StripTags',
            ],
            [
                'name' => 'StringTrim',
            ],
        ],
        'validators' => [
            [
                'name' => 'StringLength',
                'options' => [
                    'max' => 32,
                ],
            ],
        ],
    ];

    /**
     * LocalesTextFieldset constructor.
     *
     * @param int|null|string $name
     * @param array           $options
     */
    public function __construct($name, array $options = [])
    {
        if  (isset($options['element_specification'])) {
            $this->setElementSpecification($options['element_specification']);
        }

        if (isset($options['locales'])) {
            $this->setLocales($options['locales']);
        }

        parent::__construct($name, $options);
        $this->initiate();
    }

    /**
     * Initiate
     */
    public function initiate()
    {
        $elementSpecification = $this->getElementSpecification();

        foreach ($this->getLocales() as $locale => $language) {
            $this->add(
                [
                    'name'      => $locale,
                    'type'      => 'Text',
                    'options'   => [
                        'label' => $language,
                    ],
                ]
            );

            $this->inputFilterSpecification[$locale] = array_merge(
                $elementSpecification,
                ['name' => $locale]
            );
        }
    }

    //region Getters/Setters
    /**
     * @return array
     * @throws BadMethodCallException
     */
    public function getLocales()
    {
        if (null === $this->locales) {
            throw new BadMethodCallException(
                "Undefined \$locales array expected."
            );
        }

        return $this->locales;
    }

    /**
     * @param   array   $locales
     * @return  self
     */
    public function setLocales(array $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpecification;
    }

    /**
     * @param   array   $inputFilterSpecification
     * @return  self
     */
    public function setInputFilterSpecification(array $inputFilterSpecification)
    {
        $this->inputFilterSpecification = $inputFilterSpecification;

        return $this;
    }

    /**
     * @return array
     */
    public function getElementSpecification()
    {
        return $this->elementSpecification;
    }

    /**
     * @param array $elementSpecification
     * @return self
     */
    public function setElementSpecification(array $elementSpecification)
    {
        $this->elementSpecification = $elementSpecification;

        return $this;
    }
    //endregion
}