<?php
/**
 * Tour
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
 * @ORM\Table(name="soccer_tournament_season_tour")
 */
class Tour
{
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
     * @var Stage
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="tours")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $stage;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Match", mappedBy="tour")
     */
    protected $matches;

    /**
     * Winner of tour for cups or play off's only
     *
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $winner;

    /**
     * Tour constructor.
     */
    public function __construct()
    {
        $this->matches = new ArrayCollection();
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
    //endregion
}