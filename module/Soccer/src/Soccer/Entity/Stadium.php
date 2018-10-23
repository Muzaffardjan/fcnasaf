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
use Doctrine\ORM\Mapping as ORM;
use Soccer\Exception\BadMethodCallException;

/**
 * Stadium
 *
 * @ORM\Entity(repositoryClass="Soccer\Repository\StadiumsRepository")
 * @ORM\Table(name="soccer_stadium")
 */
class Stadium
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
     * @ORM\Column(type="array")
     */
    protected $name;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $located;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Match", mappedBy="stadium")
     */
    protected $matches;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Stadium constructor.
     */
    public function __construct()
    {
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

        if (strlen($name) > 4 && strtolower(substr($name, 3, 4)) == 'name') {
            $locale = str_replace('_', '-', substr($name, 7));

            return $this->getName($locale);
        }

        if (strlen($name) > 6 && strtolower(substr($name, 3, 6)) == 'located') {
            $locale = str_replace('_', '-', substr($name, 9));

            return $this->getLocated($locale);
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
     * @param null|string $ns
     * @return nul|string
     */
    public function getLabel($ns = null)
    {
        if (!$ns) {
            return $ns;
        }

        return sprintf(
            "%s, \"%s\"",
            $this->getLocated($ns),
            $this->getName($ns)
        );
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
     * @return self
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
    public function getLocated($ns = null)
    {
        if (null === $ns) {
            return $this->located;
        } elseif (isset($this->located[$ns])) {
            return $this->located[$ns];
        }

        return null;
    }

    /**
     * @param array|string $located
     * @param null|string  $ns
     * @return static
     */
    public function setLocated($located, $ns = null)
    {
        if (is_array($located)) {
            $this->located = $located;
        } elseif (null !== $ns) {
            $this->located[$ns] = $located;
        }

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
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Club $owner
     * @return self
     */
    public function setOwner(Club $owner)
    {
        $this->owner = $owner;

        return $this;
    }
    //endregion
}