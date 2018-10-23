<?php
/**
 * Staff entity
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Soccer\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Soccer\Repository\StaffRepository")
 * @ORM\Table(name="staff")
 */
class Staff
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $photo;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $lastname;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $birthDate;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $inClubSince;

    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    protected $position;

    /**
     * @ORM\ManyToOne(targetEntity="Soccer\Entity\StaffGroup", inversedBy="members")
     * @var StaffGroup
     */
    protected $group;

    #region Get/Set
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Staff
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     * @return Staff
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Staff
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Staff
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
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
     * @return Staff
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getInClubSince()
    {
        return $this->inClubSince;
    }

    /**
     * @param int $inClubSince
     * @return Staff
     */
    public function setInClubSince($inClubSince)
    {
        $this->inClubSince = $inClubSince;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return Staff
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return StaffGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param StaffGroup $group
     * @return Staff
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }
    #endregion

    public function toArray()
    {
        return [
            'id'            => $this->getId(),
            'photo'         => $this->getPhoto(),
            'firstname'     => $this->getFirstname(),
            'lastname'      => $this->getLastname(),
            'birthDate'     => $this->getBirthDate(),
            'inClubSince'   => $this->getInClubSince(),
            'position'      => $this->getPosition(),
            'group'         => [
                'id'    => $this->getGroup()->getId(),
                'name'  => $this->getGroup()->getName(),
            ],
        ];
    }
}