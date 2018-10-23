<?php 
/**
 * Articles view helper
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Application\ObjectManagerAwareTrait;
use Articles\Entity\Article;

class ArticlesHelper extends AbstractHelper 
{
    use ObjectManagerAwareTrait;

    const DEFAULT_MAX_COUNT = 10;

    /**
     * @var int 
     */
    protected $displayCount;

    /**
     * @var string 
     */
    protected $partial  = 'articles/partial/articles-helper.phtml';

    public function __construct(ObjectManager $objectManager)
    {
        $this->setObjectManager($objectManager);
    }

    public function __invoke()
    {
        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Gets the value of displayCount.
     *
     * @return int
     */
    public function getDisplayCount()
    {
        return $this->displayCount ? $this->displayCount : self::DEFAULT_MAX_COUNT;
    }

    /**
     * Sets the value of displayCount.
     *
     * @param int $displayCount the display count
     *
     * @return self
     */
    public function setDisplayCount($displayCount)
    {
        $this->displayCount = $displayCount;

        return $this;
    }

    /**
     * Gets the value of partial.
     *
     * @return string
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * Sets the value of partial.
     *
     * @param string $partial the partial
     *
     * @return self
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }


    /**
     * Renders view
     *
     * @return mixed
     */
    public function render()
    {
        return $this->getView()->partial(
            $this->getPartial(),
            [
                'articles' => $this->getObjectManager()->getRepository(
                        'Articles\Entity\Article'
                    )->findDesc(
                        $this->getDisplayCount(),
                        true,
                        true,
                        [
                            'locale' => $this->getView()->locale()->current()
                        ]
                    )
            ]
        );
    }
}