<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Soccer\Exception\BadMethodCallException;

/**
 * PlayerPosition
 *
 * @ORM\Entity(repositoryClass="Soccer\Repository\PlayerPositionsRepository")
 * @ORM\Table(name="soccer_player_position")
 */
class PlayerPosition
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $label;

    /**
     * @var array
     * @ORM\Column(type="array", name="plural_label", nullable=true)
     */
    protected $pluralLabel;

    /**
     * @var int
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    protected $order;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments = [])
    {
        if (strlen($name) > 5 && strtolower(substr($name, 3, 5)) == 'label') {
            $locale = str_replace('_', '-', substr($name, 8));

            return $this->getLabel($locale);
        }
        
        throw new BadMethodCallException(
            sprintf(
                'Method with name %s does not exists in %s class',
                $name,
                self::class
            )
        );
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getLabel($ns = null)
    {
        if (null === $ns) {
            return $this->label;
        }

        if (isset($this->label[$ns])) {
            return $this->label[$ns];
        }

        return null;
    }

    /**
     * @param   mixed $label
     * @param   null|string $ns
     * @return  self
     */
    public function setLabel(array $label, $ns = null)
    {
        if (is_array($label)) {
            $this->label = $label;
        } elseif (null !== $ns) {
            $this->label[$ns] = $label;
        }

        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getPluralLabel($ns = null)
    {
        if (null === $ns) {
            return $this->pluralLabel;
        }

        if (isset($this->pluralLabel[$ns])) {
            return $this->pluralLabel[$ns];
        }

        return null;
    }

    /**
     * @param   mixed $pluralLabel
     * @param   null|string $ns
     * @return  self
     */
    public function setPluralLabel(array $pluralLabel, $ns = null)
    {
        if (is_array($pluralLabel)) {
            $this->pluralLabel = $pluralLabel;
        } elseif (null !== $ns) {
            $this->pluralLabel[$ns] = $pluralLabel;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}