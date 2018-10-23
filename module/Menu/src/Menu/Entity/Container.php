<?php 
/**
 * Menu entity
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="menu_containers")
 * @ORM\Entity
 */
class Container
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @var string
     */
    protected $label;

    /**
     * @ORM\ManyToMany(targetEntity="Page", cascade={"persist", "remove", "merge"})
     * @ORM\JoinTable(name="menu_container_pages",
     *      joinColumns={@ORM\JoinColumn(name="container", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="pages", referencedColumnName="id", unique=true)}
     * )
     * @var ArrayCollection
     */
    protected $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $pages = [];
        
        foreach ($this->getPages() as $page) {
            $pages[] = $page->toArray();
        }

        return $pages;
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
     * Gets the value of label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the value of label.
     *
     * @param   string $label the label
     * @return  self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets pages of container
     *
     * @return ArrayCollection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Sets the pages of container
     *
     * @param   ArrayCollection $pages the pages
     * @return  self
     */
    public function setPages(ArrayCollection $pages)
    {
        $this->pages = $pages;

        return $this;
    }
}