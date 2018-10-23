<?php 
/**
 * PhotoGalleryInfo
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="media_photos_gallery_info")
 * @ORM\Entity(repositoryClass="Media\Repository\PhotoGalleryInfoRepository")
 */
class PhotoGalleryInfo 
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
    protected $title;

    /**
     * @ORM\Column(type="string", length=16)
     * @var string 
     */
    protected $locale;

    /**
     * @ORM\Column(type="text")
     * @var string 
     */
    protected $uri;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoGallery", inversedBy="info")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")
     * @var PhotoGallery
     */
    protected $gallery;

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
     * Gets the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of title.
     *
     * @param string $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the value of locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the value of locale.
     *
     * @param string $locale the locale
     *
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets the value of uri.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets the value of uri.
     *
     * @param string $uri the uri
     *
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

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
} 