<?php 
/**
 * Menu element entity
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Menu\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Navigation\AbstractContainer as NavigationContainer;
use Zend\Navigation\Page\AbstractPage as NavigationPage;
use Menu\Exception;

/**
 * @ORM\Table(name="menu_elements")
 * @ORM\Entity
 */
class Page
{
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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @var array
     */
    protected $options;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $fragment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $class;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     * @var string
     */
    protected $target;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @var array
     */
    protected $permission;

    /**
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     * @var float
     */
    protected $order;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var bool
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var bool
     */
    protected $visible;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent", cascade={"persist", "remove", "merge"})
     * @var ArrayCollection
     */
    protected $pages;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pages")
     * @var Page
     */
    protected $parent;

    /**
     * @var array $magicFields
     */
    private static $magicFields = ['uri', 'action', 'controller', 'params', 'route', 'info'];

    /**
     * Create instance from Zend\Navigation\AbstractContainer
     *
     * @param  NavigationContainer $zf2Page
     * @return Page|Container
     */
    public static function createFromZendNavigationPage(NavigationContainer $zf2Page)
    {
        if ($zf2Page instanceof NavigationPage) {
            return self::factory($zf2Page->toArray());
        } else {
            $container       = new Container();
            
            foreach ($zf2Page->toArray() as $page) {
                $container->getPages()->add(
                    self::factory($page)
                );
            }

            return $container;
        }
    }

    /**
     * Factory
     *
     * @param   array $options
     * @return  self
     */
    public static function factory(array $options)
    {
        $instance    = new static();
        $magicFields = array_fill_keys(self::$magicFields, true);

        if (isset($options['pages']) && $options['pages']) {
            foreach ($options['pages'] as $page) {
                $instance->getPages()->add(
                    self::createFromZendNavigationPage($page)
                );
            }
        }

        unset($options['pages']);

        foreach ($options as $property => $value) {
            if (isset($magicFields[$property])
                || method_exists($instance, 'set'.ucfirst($property))
            ) {
                call_user_func_array(
                    [$instance, 'set'.ucfirst($property)],
                    [$value]
                );
            } else if (!is_object($value)) { // we can't save object to db
                $instance->{$property} = $value;
            }
        }

        return $instance;
    }

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * Magic get
     *
     * @return mixed
     */
    public function __get($name) 
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        } else {
            return null;
        }
    }

    /**
     * Magic set
     *
     * @param   string  $name
     * @param   mixed   $value
     */
    public function __set($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Magic call
     *
     * @param   string  $method
     * @param   array   $args
     * @return  mixed
     */
    public function __call($method, array $args)
    {
        $access   = substr($method, 0, 3);
        $property = lcfirst(substr($method, 3));

        if (!in_array($property, self::$magicFields)) {
            throw new Exception\BadMethodCallException();
        }

        if ($access === 'set') {
            $this->{$property} = current($args);

            return $this;
        } else if ($access === 'get') {
            return $this->{$property};
        }
    }

    /**
     * Retruns array form of element
     *
     * @return array
     */
    public function toArray()
    {
        $pages    = [];
        $typeData = [];

        foreach ($this->getPages() as $page) {
            $pages[] = $page->toArray();
        }

        if ($pages) {
            $pages = ['pages' => $pages];
        }

        $typeData = [
            'uri' => $this->getUri() ? $this->getUri() : '#',
        ];

        if (isset($this->options['route']) && $this->options['route']) {
            $typeData = [
                'action'     => $this->getAction(),
                'controller' => $this->getController(),
                'params'     => $this->getParams(),
                'route'      => $this->getRoute(),
            ];
        }
 
        return array_merge(
            [
                'id'         => $this->getId(),
                'locale'     => $this->getLocale(),
                'label'      => $this->getLabel(),
                'title'      => $this->getTitle(),
                'fragment'   => $this->getFragment(),
                'class'      => $this->getClass(),
                'target'     => $this->getTarget(),
                'permission' => $this->getPermission(),
                'order'      => $this->getOrder(),
                'active'     => $this->getActive(),
                'parent_entity' => $this->getParent(),
                'visible'    => $this->getVisible(),
                'options'    => $this->getOptions()
            ],            
            $typeData,
            $pages
        );
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
     * @param string $label the label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

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
     * Gets the value of options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options ?: [];
    }

    /**
     * Sets the value of options.
     *
     * @param array $options the options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of fragment.
     *
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Sets the value of fragment.
     *
     * @param string $fragment the fragment
     *
     * @return self
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * Gets the value of class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the value of class.
     *
     * @param string $class the class
     *
     * @return self
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Gets the value of target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the value of target.
     *
     * @param string $target the target
     *
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Gets the value of permission.
     *
     * @return array
     */
    public function getPermission()
    {
        return $this->permission ?: null;
    }

    /**
     * Sets the value of permission.
     *
     * @param string|array $permission the permission
     *
     * @return self
     */
    public function setPermission($permission)
    {
        if (is_array($permission)) {
            $this->permission = $permission;
        } else if ($permission) {
            $this->permission = [$permission];
        }

        return $this;
    }

    /**
     * Gets the value of order.
     *
     * @return float
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the value of order.
     *
     * @param float $order the order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Gets the value of active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Sets the value of active.
     *
     * @param bool $active the active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Gets the value of visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Sets the value of visible.
     *
     * @param bool $visible the visible
     *
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Gets the value of pages.
     *
     * @return ArrayCollection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Sets the value of pages.
     *
     * @param ArrayCollection $pages the pages
     *
     * @return self
     */
    public function setPages(ArrayCollection $pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Gets the value of parent.
     *
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the value of parent.
     *
     * @param Page $parent the parent
     *
     * @return self
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }
}