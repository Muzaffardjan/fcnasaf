<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchStatistics
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_statistics")
 */
class MatchStatistics
{
    const TYPE_PERCENT  = 'percent';
    const TYPE_QUANTITY = 'quantity';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $label = [];

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $hostValue = 0;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $guestValue = 0;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="matchStatistics")
     */
    protected $match;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getLabel($ns = null)
    {
        if (null === $ns) {
            return $this->label;
        } elseif (isset($this->label[$ns])) {
            return $this->label[$ns];
        }

        return null;
    }

    /**
     * @param array $label
     * @return self
     */
    public function setLabel(array $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return int
     */
    public function getHostValue()
    {
        return $this->hostValue;
    }

    /**
     * @param int $hostValue
     * @return self
     */
    public function setHostValue($hostValue)
    {
        $this->hostValue = $hostValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getGuestValue()
    {
        return $this->guestValue;
    }

    /**
     * @param int $guestValue
     * @return self
     */
    public function setGuestValue($guestValue)
    {
        $this->guestValue = $guestValue;

        return $this;
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param Match $match
     * @return self
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;

        return $this;
    }
}