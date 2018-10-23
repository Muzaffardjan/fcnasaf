<?php
/**
 * TournamentSeason
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
 * @ORM\Entity
 * @ORM\Table(name="soccer_tournament_season")
 */
class Season
{
    const TYPE_CUP          = 'cup';
    const TYPE_LEAGUE       = 'league';
    const TYPE_CHAMPIONSHIP = 'championship';
    const TYPE_EXHIBITION   = 'exhibition games';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $label = [];

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    protected $type;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, name="`order`")
     */
    protected $order;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $visible;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="season", cascade={"remove"})
     */
    protected $stages;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Player", inversedBy="seasons")
     * @ORM\JoinTable(name="soccer_tournament_seasons_to_players")
     */
    protected $players;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Club", inversedBy="seasons")
     * @ORM\JoinTable(name="soccer_tournament_seasons_to_clubs")
     * @ORM\OrderBy({"alias" = "ASC"})
     */
    protected $clubs;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Match", mappedBy="season")
     */
    protected $matches;

    /**
     * @var Tournament
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="seasons")
     */
    protected $tournament;

    /**
     * Season constructor.
     */
    public function __construct()
    {
        $this->stages = new ArrayCollection();
        $this->players = new ArrayCollection();
        $this->clubs = new ArrayCollection();
        $this->matches = new ArrayCollection();
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments = [])
    {
        if (strlen($name) > 5 && strtolower(substr($name, 3, 5)) == 'label') {
            $locale = str_replace('_', '-', substr($name, 8));

            return $this->getLabel($locale);
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
     * @param  string|null  $ns
     * @return array|string
     */
    public function getLabel($ns = null)
    {
        if (null === $ns) {
            return $this->label;
        }

        if (isset($this->label[$ns])) {
            return $this->label[$ns];
        }

        return null;
    }

    /**
     * @param array|string $label
     * @param null|string  $ns
     * @return self
     */
    public function setLabel($label, $ns = null)
    {
        if (is_array($label)) {
            $this->label = $label;
        } elseif (null !== $ns) {
            $this->label[$ns] = $label;
        }

        return $this;
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
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStages()
    {
        return $this->stages;
    }

    /**
     * @param   Stage $stage
     * @return  self
     */
    public function addStage(Stage $stage)
    {
        $this->getStages()->add($stage);

        return $this;
    }

    /**
     * @param ArrayCollection $stages
     * @return self
     */
    public function setStages(ArrayCollection $stages)
    {
        $this->stages = $stages;

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
     * @return self
     */
    public function setPlayers(ArrayCollection $players)
    {
        $this->players = $players;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getClubs()
    {
        return $this->clubs;
    }

    /**
     * @param ArrayCollection $clubs
     * @return self
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
     * @return self
     */
    public function setMatches(ArrayCollection $matches)
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * @return Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * @param Tournament $tournament
     * @return self
     */
    public function setTournament(Tournament $tournament)
    {
        $this->tournament = $tournament;

        return $this;
    }
    //endregion
}