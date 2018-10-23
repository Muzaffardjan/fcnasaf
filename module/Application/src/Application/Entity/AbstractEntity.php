<?php 
/**
 * Abstract entity class
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Entity;

use \ReflectionClass;
use \ReflectionProperty;

abstract class AbstractEntity
{
    /**
     * const string 
     */
    const PRIORITY_SET  = 'set';
    const PRIORITY_READ = 'read';
    const HIDDEN_PREFIX = '__';

    /**
     * Array of readonly properties to prevent magic set to set values
     *
     * <code>
     *      [
     *          'property1',
     *      ]
     * </code>
     * @var array $__readonly
     */
    protected $__readonly = ['id'];

    /**
     * @var array $protectedProperties
     */
    private $protectedProperties;

    /**
     * Creates new instance by provided params
     *
     * @param array|Traversable $options
     */
    public static function factory($options)
    {
        if (is_object($options) && $options instanceof \Traversable) {
            $options = iterator_to_array($options);
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
                sprintf(
                    "$options must be 'array' or 'traversable', '%s' given.",
                    gettype($options) == 'object' ?
                        get_class($options) :
                        gettype($options)
                )
            );
        }

        // Get instance of the class
        $instance = new static();
        // Get writeable properties
        $properties = $instance->getAllowedProperties(
            AbstractEntity::PRIORITY_SET
        );
        
        // Set values
        foreach ($options as $key => $value) {
            if (isset($properties[$key])) {
                $instance->{$key} = $value;
            }
        }

        return $instance;
    }

    /**
     * Magic set, sets values to protected properties of an object
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $properties = $this->getAllowedProperties(self::PRIORITY_SET);

        if (isset($properties[$property])) {
            $this->$property = $value;
            return;
        }

        // trigger notice
        // property not defined
        trigger_error(
            sprintf(
                "Trying to access undefined property '%s'",
                $property
            )
        );
        return null;
    }

    /**
     * Magic get, gets protected values of an entity
     *
     * @param string $property
     */
    public function __get($property)
    {
        $properties = $this->getAllowedProperties(self::PRIORITY_READ);

        if (isset($properties[$property])) {
            return $this->$property;
        }

        // trigger notice
        // property not defined
        trigger_error(
            sprintf(
                "Trying to access undefined property '%s'",
                $property
            )
        );
        return null;
    }

    /**
     * Gets array form of object
     *
     * @return array
     */
    public function toArray()
    {
        $properties = $this->getAllowedProperties(self::PRIORITY_READ);
        $array      = [];

        foreach ($properties as $key => $value) {
            $array[$key] = $this->{$key};
        }

        return $array;
    }

    /**
     * Gets allowed to interact properties of an object
     *
     * @param string $priority
     * @return array
     */
    public function getAllowedProperties($priority = self::PRIORITY_SET)
    {
        if (
            null === $this->protectedProperties
            || !isset($this->protectedProperties[$priority])
        ) {
            $this->protectedProperties[$priority] = [];
            $reflection = new ReflectionClass($this);
            $protected  = $reflection->getProperties(
                ReflectionProperty::IS_PROTECTED
            );
            $hidden = [
                'prefix' => self::HIDDEN_PREFIX,
                'length' => strlen(self::HIDDEN_PREFIX),
            ];

            foreach ($protected as $property) {
                if (
                    (self::PRIORITY_READ !== $priority
                     && in_array($property->getName(), $this->__readonly))
                    || $hidden['prefix'] == substr(
                        $property->getName(), 
                        0, 
                        $hidden['length']
                    )
                ) {
                    continue;
                }

                $this->protectedProperties[$priority][$property->getName()] 
                = true;
            }
        }

        return $this->protectedProperties[$priority];
    }
}