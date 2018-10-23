<?php
/**
 * Card
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
 * @ORM\Table(name="soccer_card")
 */
class Card
{
    /**
     * @const string Card color red
     */
    const COLOR_RED = 'red';

    /**
     * @const string Card color yellow
     */
    const COLOR_YELLOW = 'yellow';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=10)
     */
    protected $color;

    /**
     * @var Foul
     * @ORM\OneToOne(targetEntity="Foul", inversedBy="card")
     */
    protected $foul;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="cards")
     */
    protected $match;

    /**
     * @var MatchEvent
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="card")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="cards")
     */
    protected $player;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="cards")
     */
    protected $club;

    //region Getter/Setters
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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Card
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return Foul
     */
    public function getFoul()
    {
        return $this->foul;
    }

    /**
     * @param Foul $foul
     * @return Card
     */
    public function setFoul($foul)
    {
        $this->foul = $foul;
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
     * @return Card
     */
    public function setMatch($match)
    {
        $this->match = $match;
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
     * @return Card
     */
    public function setMatchEvent($matchEvent)
    {
        $this->matchEvent = $matchEvent;
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
     * @return Card
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
     * @return Card
     */
    public function setClub($club)
    {
        $this->club = $club;
        return $this;
    }
    //endregion
}