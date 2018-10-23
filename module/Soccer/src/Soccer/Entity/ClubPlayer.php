<?php
/**
 * ClubPlayer
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Soccer\Exception\BadMethodCallException;

/**
 * @ORM\Entity(repositoryClass="Soccer\Repository\ClubPlayersRepository")
 * @ORM\Table(name="club_player")
 */
class ClubPlayer
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    protected $playing = true;

    /**
     * @var int
     * @ORM\Column(type="string", nullable=true)
     */
    protected $number;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $since;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $until;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="players")
     */
    protected $club;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="clubs")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $player;

    /**
     * @var PlayerPosition
     * @ORM\ManyToOne(targetEntity="PlayerPosition")
     */
    protected $position;

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments = [])
    {
        if (strlen($name) > 4 && strtolower(substr($name, 3, 4)) == 'name') {
            $locale = str_replace('_', '-', substr($name, 7));

            return $this->getName($locale);
        }

        if (strlen($name) > 13 && strtolower(substr($name, 3, 13)) == 'positionlabel') {
            $locale = str_replace('_', '-', substr($name, 16));

            return $this->getPositionLabel($locale);
        }

        throw new BadMethodCallException(
            sprintf(
                'Method with name %s does not exists in %s class',
                $name,
                self::class
            )
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isPlaying()
    {
        return $this->playing;
    }

    /**
     * @param bool $playing
     * @return ClubPlayer
     */
    public function setPlaying($playing)
    {
        $this->playing = $playing;

        return $this;
    }

    /**
     * @return string
     */
    public function getName($ns = null)
    {
        return sprintf(
            "%s %s",
            $this->getPlayer()->getFirstName($ns),
            $this->getPlayer()->getLastName($ns)
        );
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return ClubPlayer
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * @param \DateTime $since
     * @return ClubPlayer
     */
    public function setSince($since)
    {
        $this->since = $since;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * @param \DateTime $until
     * @return ClubPlayer
     */
    public function setUntil($until)
    {
        $this->until = $until;
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
     * @return ClubPlayer
     */
    public function setClub($club)
    {
        $this->club = $club;
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
     * @return ClubPlayer
     */
    public function setPlayer($player)
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @return PlayerPosition
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param $ns
     * @return array|string
     */
    public function getPositionLabel($ns = null) {
        return $this->getPosition()->getLabel($ns);
    }

    /**
     * @param PlayerPosition $position
     * @return self
     */
    public function setPosition(PlayerPosition $position)
    {
        $this->position = $position;

        return $this;
    }
}