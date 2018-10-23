<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PenaltySeries
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_penalty_series")
 */
class PenaltySeries
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="penaltySeries")
     */
    protected $goals;

    /**
     * @var Match
     * @ORM\OneToOne(targetEntity="Match", inversedBy="penaltySeries")
     */
    protected $match;

    public function __construct()
    {
        $this->goals = new ArrayCollection();
    }

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
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @param ArrayCollection $goals
     * @return self
     */
    public function setGoals(ArrayCollection $goals)
    {
        $this->goals = $goals;

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