<?php
/**
 * Foul
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
 * @ORM\Table(name="soccer_foul")
 */
class Foul
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
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="foul")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $reason;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $penalty;

    /**
     * @var Card
     * @ORM\OneToOne(targetEntity="Card", mappedBy="foul")
     */
    protected $card;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="fouls")
     */
    protected $player;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @var Goal
     * @ORM\OneToOne(targetEntity="Goal", inversedBy="foul")
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
     * @return Foul
     */
    public function setMatchEvent($matchEvent)
    {
        $this->matchEvent = $matchEvent;
        return $this;
    }

    /**
     * @return array
     */
    public function getReason($ns = null)
    {
        if (null === $ns) {
            return $this->reason;
        } else if (isset($this->reason[$ns])) {
            return $this->reason[$ns];
        }

        return null;
    }

    /**
     * @param array $reason
     * @return Foul
     */
    public function setReason($reason, $ns = null)
    {
        if  (is_array($reason)) {
            $this->reason = $reason;
        } else if (null !== $ns) {
            $this->reason[$ns] = $reason;
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPenalty()
    {
        return $this->penalty;
    }

    /**
     * @param boolean $penalty
     * @return Foul
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;
        return $this;
    }

    /**
     * @return Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param Card $card
     * @return Foul
     */
    public function setCard($card)
    {
        $this->card = $card;
        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param Player $player
     * @return Foul
     */
    public function setPlayer($player)
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
     * @return Foul
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;
        return $this;
    }
    //endregion
}