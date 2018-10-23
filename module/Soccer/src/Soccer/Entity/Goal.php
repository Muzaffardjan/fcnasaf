<?php
/**
 * Goal
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
 * @ORM\Table(name="soccer_goal")
 */
class Goal
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
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="goal")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

    /**
     * @var PenaltySeries
     * @ORM\ManyToOne(targetEntity="PenaltySeries", inversedBy="goals")
     */
    protected $penaltySeries;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="goals")
     */
    protected $scorer;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="assists")
     */
    protected $assist;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $fromClub;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $toClub;

    /**
     * @var Foul
     * @ORM\OneToOne(targetEntity="Foul", mappedBy="goal")
     */
    protected $foul;

    /**
     * @var CornerKick
     * @ORM\OneToOne(targetEntity="CornerKick")
     */
    protected $cornerKick;

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
     * @return Goal
     */
    public function setMatchEvent($matchEvent)
    {
        $this->matchEvent = $matchEvent;
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
     * @return Player
     */
    public function getScorer()
    {
        return $this->scorer;
    }

    /**
     * @param Player $scorer
     * @return Goal
     */
    public function setScorer($scorer)
    {
        $this->scorer = $scorer;
        return $this;
    }

    /**
     * @return Player
     */
    public function getAssist()
    {
        return $this->assist;
    }

    /**
     * @param Player $assist
     * @return Goal
     */
    public function setAssist($assist)
    {
        $this->assist = $assist;
        return $this;
    }

    /**
     * @return Club
     */
    public function getFromClub()
    {
        return $this->fromClub;
    }

    /**
     * @param Club $fromClub
     * @return Goal
     */
    public function setFromClub($fromClub)
    {
        $this->fromClub = $fromClub;
        return $this;
    }

    /**
     * @return Club
     */
    public function getToClub()
    {
        return $this->toClub;
    }

    /**
     * @param Club $toClub
     * @return Goal
     */
    public function setToClub($toClub)
    {
        $this->toClub = $toClub;
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
     * @return Goal
     */
    public function setFoul($foul)
    {
        $this->foul = $foul;
        return $this;
    }

    /**
     * @return CornerKick
     */
    public function getCornerKick()
    {
        return $this->cornerKick;
    }

    /**
     * @param CornerKick $cornerKick
     * @return Goal
     */
    public function setCornerKick($cornerKick)
    {
        $this->cornerKick = $cornerKick;
        return $this;
    }
    //endregion
}