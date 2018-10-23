<?php
/**
 * Club
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
 * @ORM\Entity(repositoryClass="Soccer\Repository\ClubsRepository")
 * @ORM\Table(name="soccer_club")
 */
class Club 
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
     * @ORM\Column(type="string", length=64)
     */
    protected $alias;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $name;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $tableName;

    /**
     * @var string
     * @ORM\Column(type="text", length=1024, nullable=true)
     */
    protected $logo;

    /**
     * @var string
     * @ORM\Column(type="text", length=1024, nullable=true)
     */
    protected $smallLogo;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $founded;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ClubPlayer", mappedBy="club", cascade={"remove"})
     */
    protected $players;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Season", mappedBy="clubs")
     */
    protected $seasons;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club", inversedBy="subClubs")
     */
    protected $parentClub;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Club", mappedBy="parentClub")
     */
    protected $subClubs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Card", mappedBy="club")
     */
    protected $cards;

    /**
     * Club constructor.
     */
    public function __construct()
    {
        $this->cards        = new ArrayCollection();
        $this->players      = new ArrayCollection();
        $this->compositions = new ArrayCollection();
        $this->subClubs     = new ArrayCollection();
    }

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

        throw new BadMethodCallException(
            sprintf(
                'Method with name %s does not exists in %s class',
                $name,
                self::class
            )
        );
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
     * @return Club
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
    public function getName($ns = null)
    {
        if (null === $ns) {
            return $this->name;
        } elseif (isset($this->name[$ns])) {
            return $this->name[$ns];
        }

        return null;
    }

    /**
     * @param array|string $name
     * @param null|string $ns
     * @return Club
     */
    public function setName($name, $ns = null)
    {
        if (is_array($name)) {
            $this->name = $name;
        } elseif (null !== $ns) {
            $this->name[$ns] = $name;
        }

        return $this;
    }

    /**
     * @param null|string $ns
     * @return array|string
     */
    public function getTableName($ns = null)
    {
        if (null === $ns) {
            return $this->tableName;
        } elseif (isset($this->tableName[$ns])) {
            return $this->tableName[$ns];
        }

        return null;
    }

    /**
     * @param array|string $tableName
     * @param null|string $ns
     * @return Club
     */
    public function setTableName($tableName, $ns = null)
    {
        if (is_array($tableName)) {
            $this->tableName = $tableName;
        } elseif (null !== $ns) {
            $this->tableName[$ns] = $tableName;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return Club
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return string
     */
    public function getSmallLogo()
    {
        return $this->smallLogo;
    }

    /**
     * @param string $smallLogo
     * @return Club
     */
    public function setSmallLogo($smallLogo)
    {
        $this->smallLogo = $smallLogo;
        return $this;
    }

    /**
     * @return int
     */
    public function getFounded()
    {
        return $this->founded;
    }

    /**
     * @param int $founded
     * @return Club
     */
    public function setFounded($founded)
    {
        $this->founded = $founded;
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
     * @return Club
     */
    public function setPlayers($players)
    {
        $this->players = $players;
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
     * @return Club
     */
    public function setCards($cards)
    {
        $this->cards = $cards;
        return $this;
    }

    /**
     * @return Club
     */
    public function getParentClub()
    {
        return $this->parentClub;
    }

    /**
     * @param Club $parentClub
     * @return self
     */
    public function setParentClub(Club $parentClub = null)
    {
        $this->parentClub = $parentClub;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubClubs()
    {
        return $this->subClubs;
    }

    /**
     * @param ArrayCollection $subClubs
     * @return self
     */
    public function setSubClubs(ArrayCollection $subClubs)
    {
        $this->subClubs = $subClubs;

        return $this;
    }
    //endregion
}