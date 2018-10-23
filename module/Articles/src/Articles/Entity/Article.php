<?php 
/**
 * Article
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Entity;

use Doctrine\ORM\Mapping as ORM;
use Users\Entity\User;
use DateTime;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity(repositoryClass="Articles\Repository\ArticlesRepository")
 */
class Article 
{
    const STATUS_DRAFT  = -1;
    const STATUS_HIDDEN = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SCHEDULED = 2;

    // events name
    const EVENT_ONADD     = 'articles.on.create';
    const EVENT_ONSCHEDULED = 'articles.on.schedule';
    const EVENT_ONDRAFT   = 'articles.on.draft.create';
    const EVENT_ONEDIT    = 'articles.on.edit';
    const EVENT_ONDELETE  = 'articles.on.delete';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @var string
     */
    protected $locale;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $uri;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $body;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="articles")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="\Users\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    protected $published;

    /**
     * @ORM\Column(type="smallint")
     * @var int
     */
    protected $status;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @var int
     */
    protected $views;

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
     * Gets the value of image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of title.
     *
     * @param string $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

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
     * Gets the value of body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the value of body.
     *
     * @param string $body the body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Gets the value of category.
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the value of category.
     *
     * @param Category $category the category
     *
     * @return self
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Gets the value of author.
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the value of author.
     *
     * @param User $author the author
     *
     * @return self
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Gets the value of published.
     *
     * @return DateTime
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Sets the value of published.
     *
     * @param DateTime $published the published
     *
     * @return self
     */
    public function setPublished(DateTime $published)
    {
        $this->published = $published;

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

    /**
     * Gets the value of views.
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views ? (int) $this->views : 0;
    }

    /**
     * Sets the value of views.
     *
     * @param int $views the views
     *
     * @return self
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }
} 