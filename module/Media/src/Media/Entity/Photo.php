<?php 
/**
 * Photo
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
 * @ORM\Table(name="media_photos")
 * @ORM\Entity
 */
class Photo 
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
    protected $source;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $thumbnail;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $smallThumbnail;

    /**
     * @ORM\Column(type="bigint")
     * @var int
     */
    protected $likes;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $shotDate;

    /**
     * @ORM\OneToMany(targetEntity="PhotoInfo", mappedBy="photo")
     * @var PhotoInfo
     */
    protected $info;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoGallery", inversedBy="photos")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")
     * @var PhotoGallery
     */
    protected $gallery;

    public function __construct()
    {
        $this->info = new ArrayCollection();
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
     * Gets the value of source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the value of source.
     *
     * @param string $source the source
     *
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Gets the value of thumbnail.
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Sets the value of thumbnail.
     *
     * @param string $thumbnail the thumbnail
     *
     * @return self
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Gets the value of smallThumbnail.
     *
     * @return string
     */
    public function getSmallThumbnail()
    {
        return $this->smallThumbnail;
    }

    /**
     * Sets the value of smallThumbnail.
     *
     * @param string $smallThumbnail the smallThumbnail
     *
     * @return self
     */
    public function setSmallThumbnail($thumbnail)
    {
        $this->smallThumbnail = $thumbnail;

        return $this;
    }

    /**
     * Gets the value of likes.
     *
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Sets the value of likes.
     *
     * @param int $likes the likes
     *
     * @return self
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return PhotoInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param PhotoInfo $info the info
     *
     * @return self
     */
    public function setInfo(PhotoInfo $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets the value of gallery.
     *
     * @return PhotoGallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Sets the value of gallery.
     *
     * @param PhotoGallery $gallery the gallery
     *
     * @return self
     */
    public function setGallery(PhotoGallery $gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Gets the value of shotDate.
     *
     * @return DateTime
     */
    public function getShotDate()
    {
        return $this->shotDate;
    }

    /**
     * Sets the value of shotDate.
     *
     * @param DateTime $shotDate the shot date
     *
     * @return self
     */
    public function setShotDate(DateTime $shotDate)
    {
        $this->shotDate = $shotDate;

        return $this;
    }
} 