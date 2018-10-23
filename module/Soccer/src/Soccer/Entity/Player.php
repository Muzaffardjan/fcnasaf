<?php
/**
 * Player entity
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Soccer\Repository\PlayersRepository")
 * @ORM\Table(name="soccer_player")
 */
class Player
{
    // Player statuses
    /**
     * @const int
     */
    const STATUS_PLAYING = 2;

    /**
     * @const int
     */
    const STATUS_FREE = 1;

    /**
     * @const int
     */
    const STATUS_END = 0;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string Alias name
     * @ORM\Column(type="string", length=64)
     */
    protected $alias;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true, name="first_name")
     */
    protected $firstName = [];

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true, name="last_name")
     */
    protected $lastName = [];

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true, name="third_name")
     */
    protected $thirdName = [];

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="birth_date")
     */
    protected $birthDate;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $height;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $weight;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $country = [];

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $photos = [];

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $settings = [];

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ClubPlayer", mappedBy="player")
     */
    protected $clubs;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Match", inversedBy="players")
     */
    protected $matches;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Season", mappedBy="players")
     */
    protected $seasons;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="scorer")
     */
    protected $goals;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Goal", mappedBy="assist")
     */
    protected $assists;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Card", mappedBy="player", cascade={"remove"})
     */
    protected $cards;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Foul", mappedBy="player", cascade={"remove"})
     */
    protected $fouls;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->goals   = new ArrayCollection();
        $this->assists = new ArrayCollection();
        $this->cards   = new ArrayCollection();
        $this->fouls   = new ArrayCollection();
    }

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $status;

    public function __toString()
    {
        return $this->getAlias();
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
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return Player
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getFirstName($ns = null)
    {
        if (null !== $ns) {
            if (isset($this->firstName[$ns])) {
                return $this->firstName[$ns];
            } else {
                return null;
            }
        }

        return $this->firstName;
    }

    /**
     * @param array|string $firstName
     * @param null|string $ns
     * @return Player
     */
    public function setFirstName($firstName, $ns = null)
    {
        if (is_array($firstName)) {
            $this->firstName = $firstName;
        } else if (null !== $ns) {
            $this->firstName[$ns] = $firstName;
        }

        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getLastName($ns = null)
    {
        if (null !== $ns) {
            if (isset($this->lastName[$ns])) {
                return $this->lastName[$ns];
            } else {
                return null;
            }
        }

        return $this->lastName;
    }

    /**
     * @param array|string $lastName
     * @param null|string $ns
     * @return Player
     */
    public function setLastName($lastName, $ns = null)
    {
        if (is_array($lastName)) {
            $this->lastName = $lastName;
        } else if (null !== $ns) {
            $this->lastName[$ns] = $lastName;
        }

        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getThirdName($ns = null)
    {
        if (null !== $ns) {
            if (isset($this->thirdName[$ns])) {
                return $this->thirdName[$ns];
            } else {
                return null;
            }
        }

        return $this->thirdName;
    }

    /**
     * @param array|string $thirdName
     * @param null|string $ns
     * @return Player
     */
    public function setThirdName($thirdName, $ns = null)
    {
        if (is_array($thirdName)) {
            $this->thirdName = $thirdName;
        } else if (null !== $ns) {
            $this->thirdName[$ns] = $thirdName;
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     * @return self
     */
    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getCountry($ns = null)
    {
        if (null !== $ns) {
            if (isset($this->country[$ns])) {
                return $this->country[$ns];
            } else {
                return null;
            }
        }

        return $this->country;
    }

    /**
     * @param string      $country
     * @param null|string $ns
     * @return Player
     */
    public function setCountry($country, $ns = null)
    {
        if (is_array($country)) {
            $this->country = $country;
        } else if (null !== $ns) {
            $this->country[$ns] = $country;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param $photo
     * @param $name
     * @return $this
     */
    public function addPhoto($photo, $name)
    {
        $this->photos[$name] = $photo;
        return $this;
    }

    /**
     * @param array $photos
     * @return Player
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return self
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getClubs($clubId = null)
    {
        if (null === $clubId) {
            return $this->clubs;
        }

        $matches = $this->clubs->matching(
            Criteria::create(
                Criteria::expr()->eq('club', $clubId)
            )
        );

        if ($matches->count()) {
            return $matches->first();
        }

        return null;
    }

    /**
     * @param ArrayCollection $clubs
     * @return $this
     */
    public function setClubs(ArrayCollection $clubs)
    {
        $this->clubs = $clubs;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @param ArrayCollection $matches
     * @return Player
     */
    public function setMatches($matches)
    {
        $this->matches = $matches;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * @param ArrayCollection $seasons
     * @return self
     */
    public function setSeasons(ArrayCollection $seasons)
    {
        $this->seasons = $seasons;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @param ArrayCollection $goals
     * @return Player
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param ArrayCollection $assists
     * @return Player
     */
    public function setAssists($assists)
    {
        $this->assists = $assists;
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
     * @return Player
     */
    public function setCards($cards)
    {
        $this->cards = $cards;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFouls()
    {
        return $this->fouls;
    }

    /**
     * @param ArrayCollection $fouls
     * @return Player
     */
    public function setFouls($fouls)
    {
        $this->fouls = $fouls;
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
    //endregion
}