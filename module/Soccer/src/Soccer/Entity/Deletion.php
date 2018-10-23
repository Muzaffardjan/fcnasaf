<?php
/**
 * Deletion
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
 * @ORM\Table(name="soccer_deletion")
 */
class Deletion
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player")
     */
    protected $player;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $club;

    /**
     * @var MatchEvent
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="deletion")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

    //region Getters/Setters
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return Deletion
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
     * @return MatchEvent
     */
    public function getMatchEvent()
    {
        return $this->matchEvent;
    }

    /**
     * @param MatchEvent $matchEvent
     * @return Deletion
     */
    public function setMatchEvent($matchEvent)
    {
        $this->matchEvent = $matchEvent;
        return $this;
    }
    //endregion
}