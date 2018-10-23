<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Soccer\Exception\BadMethodCallException;

/**
 * Stage
 *
 * @ORM\Entity(repositoryClass="Soccer\Repository\StageRepository")
 * @ORM\Table(name="soccer_tournament_season_stage")
 */
class Stage
{
    const TYPE_GROUP              = 'group';
    const TYPE_PLAY_OFF           = 'play off';
    const TYPE_SUB_STAGE          = 'sub stage';
    const TYPE_PLAY_OFF_SUB_STAGE = 'play-off sub';
    const TYPE_SINGLE             = 'single stage';

    const WINS_BY_GOALS_DIFFERENCE = 'by goal difference';
    const WINS_BY_MATCHES          = 'by matches';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    protected $type;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $label = [];

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true, name="wins_by")
     */
    protected $winsBy;

    /**
     * @var int Matches count that to make decision
     * @ORM\Column(type="smallint", nullable=true, name="matches_count")
     */
    protected $matchesCount;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="parent", cascade={"remove"})
     */
    protected $subStages;

    /**
     * @var Stage
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="subStages")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Match", mappedBy="stage")
     */
    protected $matches;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Tour", mappedBy="stage", cascade={"remove"})
     */
    protected $tours;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Series", mappedBy="stage", cascade={"remove"})
     */
    protected $series;

    /**
     * @var Season
     * @ORM\ManyToOne(targetEntity="Season", inversedBy="stages")
     */
    protected $season;

    public function __construct()
    {
        $this->matches   = new ArrayCollection();
        $this->tours     = new ArrayCollection();
        $this->series    = new ArrayCollection();
        $this->subStages = new ArrayCollection();
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

            return $this->getLabel($locale, true);
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
     * @param  string|null  $ns
     * @return array|string
     */
    public function getLabel($ns = null, $isForm = false)
    {
        if ($this->getSeason() && $isForm) {
            $result  = $this->getSeason()->getTournament()->getLabel($ns);
            $result .= ' ' . $this->getSeason()->getLabel($ns);

            if ($this->getLabel($ns)) {
                $result .= ' ' . $this->getLabel($ns);
            }

            return $result;
        }

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
    public function getWinsBy()
    {
        return $this->winsBy;
    }

    /**
     * @param string $winsBy
     * @return self
     */
    public function setWinsBy($winsBy)
    {
        $this->winsBy = $winsBy;

        return $this;
    }

    /**
     * @return int
     */
    public function getMatchesCount()
    {
        return $this->matchesCount;
    }

    /**
     * @param int $matchesCount
     * @return self
     */
    public function setMatchesCount($matchesCount)
    {
        $this->matchesCount = $matchesCount;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubStages()
    {
        return $this->subStages;
    }

    /**
     * @param ArrayCollection $subStages
     * @return self
     */
    public function setSubStages(ArrayCollection $subStages)
    {
        $this->subStages = $subStages;

        return $this;
    }

    /**
     * @return Stage
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Stage $parent
     * @return self
     */
    public function setParent(Stage $parent)
    {
        $this->parent = $parent;

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
     * @return ArrayCollection
     */
    public function getTours()
    {
        return $this->tours;
    }

    /**
     * @param ArrayCollection $tours
     * @return self
     */
    public function setTours(ArrayCollection $tours)
    {
        $this->tours = $tours;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param ArrayCollection $series
     * @return self
     */
    public function setSeries(ArrayCollection $series)
    {
        $this->series = $series;

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
    //endregion
}