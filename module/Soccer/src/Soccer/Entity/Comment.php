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
 * Comment
 *
 * @ORM\Entity
 * @ORM\Table(name="soccer_match_comment")
 */
class Comment
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    protected $locale;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @var MatchEvent
     * @ORM\OneToOne(targetEntity="MatchEvent", inversedBy="comment")
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
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

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