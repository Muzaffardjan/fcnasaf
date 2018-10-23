<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchPeriod
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_period")
 */
class MatchPeriod
{
    const TYPE_START         = 'start';
    const TYPE_HALF_TIME     = 'half time';
    const TYPE_MAIN_TIME_END = 'end of main time';
    const TYPE_FINISH        = 'finish';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    protected $type = self::TYPE_START;

    /**
     * @var MatchEvent
     * @ORM\OneToOne(targetEntity="MatchEvent", mappedBy="matchPeriod")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $matchEvent;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @return self
     */
    public function setMatchEvent(MatchEvent $matchEvent)
    {
        $this->matchEvent = $matchEvent;

        return $this;
    }
}