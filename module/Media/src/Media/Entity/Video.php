<?php 
/**
 * Video
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
 * @ORM\Table(name="media_videos")
 * @ORM\Entity(repositoryClass="Media\Repository\VideosRepository")
 */
class Video 
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
    protected $poster;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $source;

    /**
     * @ORM\Column(type="bigint")
     * @var int
     */
    protected $likes;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\OneToMany(targetEntity="VideoInfo", mappedBy="video")
     * @var VideoInfo
     */
    protected $info;

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
     * Gets the value of poster.
     *
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Sets the value of poster.
     *
     * @param string $poster the poster
     *
     * @return self
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;

        return $this;
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
     * Gets the value of date.
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the value of date.
     *
     * @param DateTime $date the date
     *
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return VideoInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param VideoInfo $info the info
     *
     * @return self
     */
    public function setInfo(VideoInfo $info)
    {
        $this->info = $info;

        return $this;
    }
} 