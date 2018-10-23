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

/**
 * MatchPlayerStatus
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_player_status")
 */
class MatchPlayerStatus
{
    const STATUS_IN_BENCH    = 0;
    const STATUS_IN_GAME     = 1;
    const STATUS_DELETED     = -1;
    const STATUS_SUBSTITUTED = 2;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, name="played_minutes")
     */
    protected $playedMinutes = 0;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $goals = 0;

    /**
     * @var int Indicates missed goals of goalkeeper
     * @ORM\Column(type="smallint", name="missed_goals", nullable=true)
     */
    protected $missedGoals = 0;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $assists = 0;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $status;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match")
     */
    protected $match;

    /**
     * @var Player
     * @ORM\OneToOne(targetEntity="Player")
     */
    protected $player;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    //region Getters/Setters

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPlayedMinutes()
    {
        return $this->playedMinutes;
    }

    /**
     * @param int $playedMinutes
     * @return self
     */
    public function setPlayedMinutes($playedMinutes)
    {
        $this->playedMinutes = $playedMinutes;

        return $this;
    }

    /**
     * @return int
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @param int $goals
     * @return self
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * @return int
     */
    public function getMissedGoals()
    {
        return $this->missedGoals;
    }

    /**
     * @param int $missedGoals
     * @return self
     */
    public function setMissedGoals($missedGoals)
    {
        $this->missedGoals = $missedGoals;

        return $this;
    }

    /**
     * @return int
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param int $assists
     * @return self
     */
    public function setAssists($assists)
    {
        $this->assists = $assists;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

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

    /**
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param Player $player
     * @return self
     */
    public function setPlayer(Player $player)
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
    //endregion
}