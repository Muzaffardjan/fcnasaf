<?php
/**
 * Match
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Soccer\Repository\MatchesRepository")
 * @ORM\Table(name="soccer_match")
 */
class Match
{
    /**
     * @const int
     */
    const LENGTH_90 = 90;

    /**
     * @const int
     */
    const LENGTH_120 = 120;

    /**
     * @const int
     */
    const STATUS_QUEUE = 0;

    /**
     * @const int
     */
    const STATUS_ONGOING = 1;

    /**
     * @const int
     */
    const STATUS_BREAK = 3;

    /**
     * @const int
     */
    const STATUS_FINISHED = 2;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     */
    protected $status = self::STATUS_QUEUE;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $length = self::LENGTH_90;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $started;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $ended;

    /**
     * @var Stadium
     * @ORM\ManyToOne(targetEntity="Stadium", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $stadium;

    /**
     * @var Season
     * @ORM\ManyToOne(targetEntity="Season", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $season;

    /**
     * @var Stage
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $stage;

    /**
     * @var Tour
     * @ORM\ManyToOne(targetEntity="Tour", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $tour;

    /**
     * @var Series
     * @ORM\ManyToOne(targetEntity="Series", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $series;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $host;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $guest;

    /**
     * Indicates winner
     * Will be set manually in exception situations
     * But most of the time will be set automatically
     *
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $winner;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Player", mappedBy="matches")
     */
    protected $players;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LineUp", mappedBy="match", cascade={"remove"})
     */
    protected $lineUp;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MatchEvent", mappedBy="match")
     * @ORM\OrderBy({"minutes": "DESC", "seconds": "DESC", "id": "DESC"})
     */
    protected $events;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Card", mappedBy="match")
     */
    protected $cards;

    /**
     * @var PenaltySeries
     * @ORM\OneToOne(targetEntity="PenaltySeries", mappedBy="match")
     */
    protected $penaltySeries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MatchStatistics", mappedBy="match")
     */
    protected $matchStatistics;

    /**
     * Match constructor.
     */
    public function __construct()
    {
        $this->events           = new ArrayCollection();
        $this->cards            = new ArrayCollection();
        $this->lineUp           = new ArrayCollection();
        $this->matchStatistics  = new ArrayCollection();
    }

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
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return Match
     */
    public function setLength($length)
    {
        $this->length = $length;
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
     * @return Match
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Match
     */
    public function setDate($date)
    {
        if ($date instanceof \DateTime && $date->getTimestamp() + 130 * 60 < time()) {
            $this->status = self::STATUS_FINISHED;
        }

        $this->date = $date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param \DateTime $started
     * @return self
     */
    public function setStarted(\DateTime $started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * @param \DateTime $ended
     * @return self
     */
    public function setEnded(\DateTime $ended)
    {
        $this->ended = $ended;

        return $this;
    }

    /**
     * @return Stadium
     */
    public function getStadium()
    {
        return $this->stadium;
    }

    /**
     * @param Stadium $stadium
     * @return self
     */
    public function setStadium(Stadium $stadium)
    {
        $this->stadium = $stadium;

        return $this;
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param Stage $stage
     * @return self
     */
    public function setStage(Stage $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @return Tour
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * @param Tour $tour
     * @return Match
     */
    public function setTour($tour)
    {
        $this->tour = $tour;
        return $this;
    }

    /**
     * @return Series
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param Series $series
     * @return self
     */
    public function setSeries(Series $series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @return Club
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param Club $host
     * @return Match
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return Club
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * @param Club $guest
     * @return Match
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param ArrayCollection $events
     * @return Match
     */
    public function setEvents($events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @param ArrayCollection $cards
     * @return Match
     */
    public function setCards($cards)
    {
        $this->cards = $cards;
        return $this;
    }

    /**
     * @return PenaltySeries
     */
    public function getPenaltySeries()
    {
        return $this->penaltySeries;
    }

    /**
     * @param PenaltySeries $penaltySeries
     * @return self
     */
    public function setPenaltySeries(PenaltySeries $penaltySeries)
    {
        $this->penaltySeries = $penaltySeries;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param Season $season
     * @return self
     */
    public function setSeason(Season $season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return Club
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @param Club $winner
     * @return self
     */
    public function setWinner(Club $winner)
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param ArrayCollection $players
     * @return Match
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLineUp()
    {
        return $this->lineUp;
    }

    /**
     * @param ArrayCollection $lineUp
     * @return self
     */
    public function setLineUp(ArrayCollection $lineUp)
    {
        $this->lineUp = $lineUp;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMatchStatistics()
    {
        return $this->matchStatistics;
    }

    /**
     * @param ArrayCollection $matchStatistics
     * @return self
     */
    public function setMatchStatistics(ArrayCollection $matchStatistics)
    {
        $this->matchStatistics = $matchStatistics;

        return $this;
    }
    //endregion
}