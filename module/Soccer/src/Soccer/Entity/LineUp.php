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
 * LineUp
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_matches_line_up")
 */
class LineUp
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="lineUp")
     */
    protected $match;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="ClubPlayer")
     * @ORM\JoinTable(name="soccer_line_up_starters_to_match",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="line_up_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $starters;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="ClubPlayer")
     * @ORM\JoinTable(name="soccer_line_up_substitutes_to_match",
     *      joinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="line_up_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $substitutes;

    public function __construct()
    {
        $this->starters     = new ArrayCollection();
        $this->substitutes  = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * @param Club $club
     * @return self
     */
    public function setClub(Club $club)
    {
        $this->club = $club;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStarters()
    {
        return $this->starters;
    }

    /**
     * @param ArrayCollection $starters
     * @return self
     */
    public function setStarters(ArrayCollection $starters)
    {
        $this->starters = $starters;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubstitutes()
    {
        return $this->substitutes;
    }

    /**
     * @param ArrayCollection $substitutes
     * @return self
     */
    public function setSubstitutes(ArrayCollection $substitutes)
    {
        $this->substitutes = $substitutes;

        return $this;
    }
}