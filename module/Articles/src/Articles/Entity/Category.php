<?php 
/**
 * Categoty
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Articles\Repository\CategoriesRepository")
 */
class Category 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $locale;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $uri;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category", cascade={"remove"})
     * @var ArrayCollection
     */
    protected $articles;

    public function __construct() 
    {
        $this->articles = new ArrayCollection();
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
     * Gets the value of articles.
     *
     * @return ArrayCollection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Sets the value of articles.
     *
     * @param ArrayCollection $articles the articles
     *
     * @return self
     */
    public function setArticles(ArrayCollection $articles)
    {
        $this->articles = $articles;

        return $this;
    }
}