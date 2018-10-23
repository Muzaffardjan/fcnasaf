<?php 
/**
 * Related articles view helper
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

class RelatedHelper extends AbstractHelper 
{
    use ObjectManagerAwareTrait;

    const DEFAULT_MAX_COUNT = 4;

    /**
     * @var string
     */
    protected $partial;

    /**
     * @var array
     */
    protected $currentResultSet;

    /**
     * @var Article
     */
    protected $target;

    /**
     * @var int
     */
    protected $maxCount;

    /**
     * {@inheritdoc}
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->setObjectManager($objectManager);
    }

    /**
     * Invoke short hand
     */
    public function __invoke(
        Article $article, 
        $maxCount = null, 
        $partial = null
    ) {
        if ($article) {
            $this->setArticle($article);
        }

        if ($maxCount) {
            $this->setMaxCount($maxCount);
        }

        if ($partial) {
            $this->setPartial($partial);
        }

        return $this->render();
    }

    /**
     * Gets related articles
     *
     * @param   Article $article
     * @param   int     $max
     * @return  array
     */
    public function getRelated(Article $article, $max = 4)
    {
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );

        $this->currentResultSet = $repository->getRelated($article, $max);

        return $this->currentResultSet;
    }

    /**
     * Sets article
     *
     * @param   Article $article
     * @return  self
     */
    public function setArticle(Article $article)
    {
        $this->target = $article;
        return $this;
    }

    /**
     * Gets article
     *
     * @return Article
     */
    public function getArticle()
    {
        return $this->target;
    }

    /**
     * Sets max result count
     *
     * @param   int $max
     * @return  self
     */
    public function setMaxCount($max)
    {
        $this->maxCount = (int) $max;
    }

    /**
     * Gets max count 
     *
     * @return int 
     */
    public function getMaxCount()
    {
        if (null === $this->maxCount) {
            $this->maxCount = self::DEFAULT_MAX_COUNT;
        }

        return $this->maxCount;
    }

    /**
     * Sets partial
     *
     * @param   string $partial
     * @return  self
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * Gets partial
     *
     * @return string
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * Renders view
     *
     * @return string
     */
    public function render()
    {
        if (null === $this->getArticle()) {
            return;
        } 

        $related = $this->getRelated(
            $this->getArticle(),
            $this->getMaxCount()
        );
        $article = $this->getArticle();

        if ($this->getPartial()) {
            return $this->getView()->partial(
                $this->getPartial(),
                [
                    'related' => $related,
                    'article' => $article,
                ]
            );
        }

        $rendered = '<ul>%s</ul>';
        $list     = '';

        foreach ($related as $article) {
            $list .= sprintf(
                "<li>%s</li>",
                $article->getTitle()
            );
        }

        return sprintf($rendered, $list);
    }
}