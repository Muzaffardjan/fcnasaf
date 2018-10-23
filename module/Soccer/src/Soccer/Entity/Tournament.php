<?php
/**
 * Tournament
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
 * @ORM\Table(name="soccer_tournament")
 */
class Tournament
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
     * @ORM\Column(type="string", name="alias_name", length=32)
     */
    protected $aliasName;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $label = [];

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Season", mappedBy="tournament", cascade={"remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $seasons;

    /**
     * Tournament constructor.
     */
    public function __construct()
    {
        $this->seasons = new ArrayCollection();
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
     * @return string
     */
    public function getAliasName()
    {
        return $this->aliasName;
    }

    /**
     * @param string $aliasName
     * @return self
     */
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;

        return $this;
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
     * @return ArrayCollection
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * Adds season to tournament
     *
     * @param   Season $season
     * @return  self
     */
    public function addSeason(Season $season)
    {
        $this->getSeasons()->add($season);

        return $this;
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
    //endregion
}