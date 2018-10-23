<?php 
/**
 * AbstractCroppingAdapter
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace ImagesManager\Thumbnail\Cropping;

use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\StringUtils;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\CamelCaseToUnderscore;
use ImagesManager\Exception;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var float $offsetX
     */
    protected $offsetX;

    /**
     * @var float $offsetY
     */
    protected $offsetY;

    /**
     * @var float $rotate
     */
    protected $rotate;

    /**
     * @var float $cropWidth
     */
    protected $cropWidth;

    /**
     * @var float $cropHeight
     */
    protected $cropHeight;

    /**
     * @var array $setters
     */
    protected static $setters;

    /**
     * Factory
     *
     * @param Traversable|array $options
     */
    public function setOptions($options)
    {
        if (is_array($options) || $options instanceof Traversable) {
            $this->options = $options;
            $options = ArrayUtils::iteratorToArray($options);
        } else {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Invalid argument \'%s\' or \'%s\' expected \'%s\' given.',
                    'array',
                    'Traversable',
                    gettype($options) == 'object' ? 
                        get_class($options) :
                        gettype($options)
                )
            );
        }

        if (null === self::$setters) {
            $setters       = ArrayUtils::filter(
                get_class_methods($this),
                function ($value) {
                    return substr($value, 0, 3) === 'set' && $value !== 'setOptions';
                }
            );
            $filters       = [
                'lowercase'  => new StringToLower(),
                'underscore' => new CamelCaseToUnderscore(),
            ];
            self::$setters = [];

            foreach ($setters as $setter) {
                $key = $filters['lowercase']->filter(
                    $filters['underscore']->filter($setter)
                );

                self::$setters[substr($key, 4)] = $setter;
            }

            unset($filters);
        }

        foreach ($options as $setter => $parametres) {
            if (!isset(self::$setters[$setter])) {
                continue;
            }

            call_user_func_array(
                [$this, self::$setters[$setter]],
                is_array($parametres) ? $parametres : [$parametres]
            );
        }

        return $this;
    }

    /**
     * Gets option
     *
     * @param   string  $key
     * @return  mixed
     */
    public function getOption($key)
    {
        if (isset($this->option[$key])) {
            return $this->option[$key];
        }

        return null;
    }

    /**
     * Gets object options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     * @return AbstractCroppingAdapter
     */
    public function setOffsetX($offsetX)
    {
        $this->offsetX = (float) $offsetX;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffsetX()
    {
        return $this->offsetX;
    }

    /**
     * {@inheritdoc}
     * @return AbstractCroppingAdapter
     */
    public function setOffsetY($offsetY)
    {
        $this->offsetY = (float) $offsetY;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffsetY()
    {
        return $this->offsetY;
    }

    /**
     * {@inheritdoc}
     * @return AbstractCroppingAdapter
     */
    public function setRotateDegree($rotate)
    {
        $this->rotate = (float) $rotate;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRotateDegree()
    {
        return $this->rotate;
    }

    /**
     * {@inheritdoc}
     * @return AbstractCroppingAdapter
     */
    public function setCropWidth($cropWidth)
    {
        $this->cropWidth = (float) $cropWidth;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCropWidth()
    {
        return $this->cropWidth;
    }

    /**
     * {@inheritdoc}
     * @return AbstractCroppingAdapter
     */
    public function setCropHeight($cropHeight)
    {
        $this->cropHeight = (float) $cropHeight;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCropHeight()
    {
        return $this->cropHeight;
    }
}