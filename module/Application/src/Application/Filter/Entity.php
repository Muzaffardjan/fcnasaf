<?php
/**
 * Entity filter mostly for form
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Application\Filter;

use Application\ObjectManagerAwareInterface;
use Application\ObjectManagerAwareTrait;
use Zend\Filter\AbstractFilter;

class Entity extends AbstractFilter implements ObjectManagerAwareInterface
{
    use ObjectManagerAwareTrait;

    /**
     * @var string
     */
    protected $entity;

    public function __construct(
        $options = null
    ) {
        if (isset($options['entity'])) {
            $this->setEntity($options['entity']);
        }

        if (isset($options['object_manager'])) {
            $this->setObjectManager($options['object_manager']);
        }
    }

    /**
     * @param int $value
     * @return mixed Target entity
     */
    public function filter($value)
    {
        return $this->getObjectManager()
            ->getRepository($this->getEntity())
            ->find((int) $value);
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     * @return Entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
}