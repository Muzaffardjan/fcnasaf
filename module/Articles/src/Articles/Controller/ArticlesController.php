<?php 
/**
 * ArticlesController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Controller;

use Articles\Entity\Article;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Application\Controller\AbstractObjectManagerAwareController;

class ArticlesController extends AbstractObjectManagerAwareController
{
    public function __construct()
    {
        // Controller only reads data
        $this->setNeedFlush(false);
    }

    /**
     * Shows page
     */
    public function articleAction()
    {
        if ($this->params('uri')) {
            $objectManager  = $this->getObjectManager();
            $repository     = $objectManager->getRepository(
                'Articles\Entity\Article'
            );
            /**
             * @var Article $article
             */
            $article = $repository->findOneBy(
                [
                    'locale' => $this->locale()->current(),
                    'uri'    => $this->params('uri'),
                ] 
            );
            
            if ($article 
                && ($article->getStatus() == Article::STATUS_ACTIVE
                    || $this->isGranted('manage_content'))
            ) {
                if (!$this->identity()) {
                    // Action needs flush after
                    $this->setNeedFlush(true);

                    if (!$this->isGranted('manage_content')) {
                        // increment views
                        $article->setViews($article->getViews() + 1);
                    }
                }

                return [
                    'article' => $article,
                ];
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    /**
     * Shows articles list (categorized)
     * Uncategorized articles will not be displayed because they accepted as pages
     */
    public function articlesListAction()
    {
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );
        $request = $this->getRequest();
        $view    = new ViewModel();

        if ($request->getQuery('search')) {
            $articles = $repository->getPaginatedSearch(
                trim($request->getQuery('search')),
                false,
                ['locale' => $this->locale()->current()],
                true,
                true
            );
        } else {
            $articles = $repository->getPaginated(
                ['locale' => $this->locale()->current()],
                true,
                true
            );
        }

        $articles = new Paginator($articles);

        $articles->setItemCountPerPage(
            $request->getQuery('limit', 10)
        );
        $articles->setCurrentPageNumber(
            $this->params('page', 1)
        );

        $variables = [
            'articles' => $articles,
            'search'   => trim($request->getQuery('search')),
            'uri'      => $request->getUri(),
            'isAjax'   => $request->isXmlHttpRequest(),
            'fragment' => $request->getQuery('fragment'),
        ];

        if ($request->isXmlHttpRequest()) {
            $view->setTerminal(true)
            ->setTemplate('articles/articles-ajax-pager');

            if ($request->getQuery('fragment')) {
                $articles->setItemCountPerPage(
                    $request->getQuery('limit', 10) * $request->getQuery('fragment')
                )
                ->setCurrentPageNumber(1);

                $variables['limit'] = $request->getQuery('limit', 10);
            }

            $totalPagesCount = ceil($articles->getTotalItemCount() / $request->getQuery('limit', 10));

            if ($totalPagesCount < $this->params('page', 1)) {
                return new JsonModel(
                    [
                        'isEnded' => true,
                    ]
                );
            }
        }

        $variables['categories'] = $this->getObjectManager()->getRepository(
            'Articles\Entity\Category'
        )
        ->findBy(['locale' => $this->locale()->current()]);

        $view->setVariables($variables);
        return $view;
    }

    /**
     * Shows articles by category
     */
    public function byCategoryAction()
    {
        if ($this->params('uri')) {
        	$categoryRepository = $this->getObjectManager()
            	->getRepository('Articles\Entity\Category');

            $request = $this->getRequest();
            $category = $categoryRepository->findOneBy(['uri' => $this->params('uri')]);
            $categories = $categoryRepository->findBy(['locale' => $this->locale()->current()]);

            if ($category) {
                $repository = $this->getObjectManager()->getRepository(
                    'Articles\Entity\Article'
                );
                $view    = new ViewModel();

                if ($request->getQuery('search')) {
                    $articles = $repository->getPaginatedSearch(
                        trim($request->getQuery('search')),
                        false,
                        [
                            'locale'    => $category->getLocale(),
                            'category'  => $category 
                        ],
                        true
                    );
                } else {
                    $articles = $repository->getPaginated(
                        [
                            'locale'    => $category->getLocale(),
                            'category'  => $category 
                        ],
                        true
                    );
                }

                $articles = new Paginator($articles);

                $articles->setItemCountPerPage(
                    $request->getQuery('limit', 10)
                );
                $articles->setCurrentPageNumber(
                    $this->params('page', 1)
                );

                $variables = [
                    'articles' => $articles,
                    'search'   => trim($request->getQuery('search')),
                    'uri'      => $request->getUri(),
                    'isAjax'   => $request->isXmlHttpRequest(),
                    'fragment' => $request->getQuery('fragment'),
                    'category' => $category,
                    'categories' => $categories,
                ];

                if ($request->isXmlHttpRequest()) {
                    $view->setTerminal(true)
                    ->setTemplate('articles/articles-ajax-pager');

                    if ($request->getQuery('fragment')) {
                        $articles->setItemCountPerPage(
                            $request->getQuery('limit', 10) * $request->getQuery('fragment')
                        )
                        ->setCurrentPageNumber(1);

                        $variables['limit'] = $request->getQuery('limit', 10);
                    }

                    $totalPagesCount = ceil($articles->getTotalItemCount() / $request->getQuery('limit', 10));

                    if ($totalPagesCount < $this->params('page', 1)) {
                        return new JsonModel(
                            [
                                'isEnded' => true,
                            ]
                        );
                    }
                }

                $view->setVariables($variables);
                return $view;
            }
        }
        
        $this->getResponse()->setStatusCode(404);
        return;
    }
}