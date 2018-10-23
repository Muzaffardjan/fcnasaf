<?php
/**
 * StaffGroup
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="staffgroup")
 */
class StaffGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Soccer\Entity\Staff", mappedBy="group", cascade={"remove"})
     * @var ArrayCollection
     */
    protected $members;

    /**
     * StaffGroup constructor.
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return StaffGroup
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param ArrayCollection $members
     * @return StaffGroup
     */
    public function setMembers($members)
    {
        $this->members = $members;
        return $this;
    }
}