<?php 
/**
 * PhotoGallery
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Table(name="media_photos_gallery")
 * @ORM\Entity(repositoryClass="Media\Repository\PhotoGalleriesRepository")
 */
class PhotoGallery 
{
    const STATUS_FINISHED = 100;
    const STATUS_DRAFT    = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $createdDate;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="gallery", cascade={"persist", "remove"})
     * @var Photo
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="PhotoGalleryInfo", mappedBy="gallery")
     * @var PhotoGalleryInfo
     */
    protected $info;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->info   = new ArrayCollection();
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of photos.
     *
     * @return Photo
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Sets the value of photos.
     *
     * @param Photo $photos the photos
     *
     * @return self
     */
    public function setPhotos(Photo $photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return PhotoGalleryInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param PhotoGalleryInfo $info the info
     *
     * @return self
     */
    public function setInfo(PhotoGalleryInfo $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets the value of createdDate.
     *
     * @return DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Sets the value of createdDate.
     *
     * @param DateTime $createdDate the crearted date
     *
     * @return self
     */
    public function setCreatedDate(DateTime $createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Gets the value of status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the value of status.
     *
     * @param int $status the status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
} 