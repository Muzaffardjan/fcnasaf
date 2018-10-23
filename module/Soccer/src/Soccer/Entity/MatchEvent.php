<?php
/**
 * MatchTime
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
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_time")
 */
class MatchEvent
{
    const TYPE_COMMENT      = 1;
    const TYPE_SHOT         = 2;
    const TYPE_GOAL         = 3;
    const TYPE_CORNER_KICK  = 4;
    const TYPE_OFFSIDE      = 5;
    const TYPE_FOUL         = 6;
    const TYPE_CARD         = 7;
    const TYPE_SUBSTITUTION = 8;
    const TYPE_DELETION     = 9;
    const TYPE_MATCH_PERIOD = 10;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="smallint", name="`type`", columnDefinition="TINYINT(2)")
     */
    protected $type = self::TYPE_COMMENT;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $minutes;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $seconds;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $extra;

    /**
     * @var Comment
     * @ORM\OneToOne(targetEntity="Comment", mappedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $comment;

    /**
     * @var Match
     * @ORM\ManyToOne(targetEntity="Match", inversedBy="events")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $match;

    /**
     * @var Shot
     * @ORM\OneToOne(targetEntity="Shot", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $shot;

    /**
     * @var Goal
     * @ORM\OneToOne(targetEntity="Goal", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $goal;

    /**
     * @var CornerKick
     * @ORM\OneToOne(targetEntity="CornerKick", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $cornerKick;

    /**
     * @var Offside
     * @ORM\OneToOne(targetEntity="Offside", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $offside;

    /**
     * @var Foul
     * @ORM\OneToOne(targetEntity="Foul", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $foul;

    /**
     * @var Card
     * @ORM\OneToOne(targetEntity="Card", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $card;

    /**
     * @var Substitution
     * @ORM\OneToOne(targetEntity="Substitution", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $substitution;

    /**
     * @var Deletion
     * @ORM\OneToOne(targetEntity="Deletion", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $deletion;

    /**
     * @var MatchPeriod
     * @ORM\OneToOne(targetEntity="MatchPeriod", inversedBy="matchEvent", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchPeriod;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return MatchEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     * @return MatchEvent
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     * @return MatchEvent
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param int $extra
     * @return MatchEvent
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     * @return self
     */
    public function setComment(Comment $comment)
    {
        $this->setType(self::TYPE_COMMENT);
        $this->comment = $comment;

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
     * @return MatchEvent
     */
    public function setMatch($match)
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return Shot
     */
    public function getShot()
    {
        return $this->shot;
    }

    /**
     * @param Shot $shot
     * @return MatchEvent
     */
    public function setShot($shot)
    {
        $this->setType(self::TYPE_SHOT);
        $this->shot = $shot;
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
     * @return MatchEvent
     */
    public function setGoal($goal)
    {
        $this->setType(self::TYPE_GOAL);
        $this->goal = $goal;
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
     * @return MatchEvent
     */
    public function setCornerKick($cornerKick)
    {
        $this->setType(self::TYPE_CORNER_KICK);
        $this->cornerKick = $cornerKick;
        return $this;
    }

    /**
     * @return Offside
     */
    public function getOffside()
    {
        return $this->offside;
    }

    /**
     * @param Offside $offside
     * @return MatchEvent
     */
    public function setOffside($offside)
    {
        $this->setType(self::TYPE_OFFSIDE);
        $this->offside = $offside;
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
     * @return MatchEvent
     */
    public function setFoul($foul)
    {
        $this->setType(self::TYPE_FOUL);
        $this->foul = $foul;
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
     * @return MatchEvent
     */
    public function setCard($card)
    {
        $this->setType(self::TYPE_CARD);
        $this->card = $card;
        return $this;
    }

    /**
     * @return Substitution
     */
    public function getSubstitution()
    {
        return $this->substitution;
    }

    /**
     * @param Substitution $substitution
     * @return MatchEvent
     */
    public function setSubstitution($substitution)
    {
        $this->setType(self::TYPE_SUBSTITUTION);
        $this->substitution = $substitution;
        return $this;
    }

    /**
     * @return Deletion
     */
    public function getDeletion()
    {
        return $this->deletion;
    }

    /**
     * @param Deletion $deletion
     * @return MatchEvent
     */
    public function setDeletion($deletion)
    {
        $this->setType(self::TYPE_DELETION);
        $this->deletion = $deletion;
        return $this;
    }

    /**
     * @return MatchPeriod
     */
    public function getMatchPeriod()
    {
        return $this->matchPeriod;
    }

    /**
     * @param MatchPeriod $matchPeriod
     * @return self
     */
    public function setMatchPeriod(MatchPeriod $matchPeriod)
    {
        $this->setType(self::TYPE_MATCH_PERIOD);

        $this->matchPeriod = $matchPeriod;

        return $this;
    }
    //endregion
}