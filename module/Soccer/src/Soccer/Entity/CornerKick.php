<?php
/**
 * CornerKick
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="soccer_corner_kick")
 */
class CornerKick
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * @var MatchEvent
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="cornerKick")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

    /**
     * @var ClubPlayer
     * @ORM\ManyToOne(targetEntity="ClubPlayer")
     */
    protected $player;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @var Goal
     * @ORM\OneToOne(targetEntity="Goal")
     */
    protected $goal;

    //region Getters/Setters
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MatchEvent
     */
    public function getMatchEvent()
    {
        return $this->matchEvent;
    }

    /**
     * @param MatchEvent $matchEvent
     * @return CornerKick
     */
    public function setMatchEvent($matchEvent)
    {
        $this->matchEvent = $matchEvent;
        return $this;
    }

    /**
     * @return ClubPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param ClubPlayer $player
     * @return self
     */
    public function setPlayer(ClubPlayer $player)
    {
        $this->player = $player;

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
     * @return Goal
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * @param Goal $goal
     * @return CornerKick
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
        return $this;
    }
    //endregion
}