<?php 
/**
 * Articles manage controller
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Controller\Manage;

use Zend\View\Model\JsonModel;
use Zend\EventManager\Event;
use Zend\Paginator\Paginator;
use Admin\Controller\AbstractObjectManagerAwareController;
use Application\Filter\FriendlyUri;
use Articles\Form\ArticlesForm;
use Articles\Entity\Article;
use Articles\Form\MenuArticle;

class ArticlesController extends AbstractObjectManagerAwareController
{
    const INSERT_IMAGE_EDITOR = 'add_image_to_editor';
    const INSERT_IMAGE_TITLE  = 'add_image_to_title';

    /**
     * @var ArticlesForm
     */
    protected $formArticles;
    protected $formMenuArticle;

    public function __construct(ArticlesForm $formArticles, MenuArticle $formMenuArticle)
    {
        $this->formArticles = $formArticles;
        $this->formMenuArticle = $formMenuArticle;
    }

    public function indexAction()
    {
        $this->layout()->tag = [
            'body' => [
                'class' => ['app-documents'],
            ],
        ];

        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );
        $articles = [];
        $request  = $this->getRequest();
        $limit    = $request->getQuery('limit', 12);

        if ($request->getQuery('search') || $request->getQuery('locale') || $request->getQuery('category')) {
            $criteria = [];

            if ($request->getQuery('locale') 
                && in_array($request->getQuery('locale'), $this->locale()->all())
            ) {
                $criteria = ['locale' => $request->getQuery('locale')];
            }

            if ($request->getQuery('search') 
                &&
                mb_strwidth($request->getQuery('search'), 'UTF-8') >= 4 
            ) {
                $articles = $repository->getPaginatedSearch(
                    $request->getQuery('search'),
                    true,
                    $criteria
                );
            } else {
                if ($request->getQuery('category')) {
                    $criteria['category'] = $request->getQuery('category');
                }
                $articles = $repository->getPaginated($criteria);
            }
        } else {
            $articles = $repository->getPaginated();
        }

        $articles = new Paginator($articles);
        $articles->setDefaultItemCountPerPage($limit);
        $articles->setCurrentPageNumber((int) $request->getQuery('page', 1));

        return [
            'articles'   => $articles,
            'count'      => $repository->getCount(),
            'query'      => $this->getRequest()->getQuery(),   
        ];
    }

    public function getCategoriesAction()
    {
        $locales = $this->locale()->all();

        if ($this->getRequest()->isXmlHttpRequest()
            && isset($locales[$this->params('id')])
        ) {
            $categories = $this->getObjectManager()
            ->getRepository('Articles\Entity\Category')
            ->getByLocale($this->params('id'));
            $result = [];

            foreach ($categories as $category) {
                $result[] = [
                    'id'    => $category->getId(),
                    'title' => $category->getTitle(),
                ];
            }

            return new JsonModel($result);
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->formArticles;
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );

        if ($this->params('id') && $article = $repository->find($this->params('id'))) {
            $form->setData(
                [
                    'locale'        => $article->getLocale(),
                    'title'         => $article->getTitle(),
                    'image'         => $article->getImage(),
                    'body'          => $article->getBody(),
                    'category'      => $article->getCategory() ? $article->getCategory()->getId() : null,
                    'hidden'        => $article->getStatus() == Article::STATUS_HIDDEN,
                    'publish_date'  => $article->getPublished() ? $article->getPublished()->format('d.m.Y') : null,
                    'publish_time'  => $article->getPublished() ? $article->getPublished()->format('H:i') : null,
                ]
            );
        }

        if ($this->apiCall()->isReturned()) {
            if ($request->getQuery('recall')) {
                return $this->redirect()->toRoute(
                    null,
                    $this->params()->fromRoute()
                );
            }

            $recall    = $this->apiCall()
            ->getCallerUri()->getQueryAsArray()['recall'];
            $apiResult = $this->apiCall()->getResult();

            switch ($recall) {
                case self::INSERT_IMAGE_TITLE:
                    $form->get('image')->setValue($apiResult['thumbnails']['article_title'][0]['href']);
                break; 
                case self::INSERT_IMAGE_EDITOR: 
                    $append = $apiResult['thumbnails']['article_body'][0]['href'];
                break;
            }
        }

        $form->get('locale')->setValueOptions($this->locale()->all());


        // Check and save data
        if ($request->isPost() && !$request->getQuery('recall')) {
            // set mode to normal article
            $form->setMode(null);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                if (!isset($article)) {
                    $article = new Article();
                }

                $data         = $form->getData();
                $friendlyUri  = new FriendlyUri('_');
                $publish_date = $data['publish_date'] ? $data['publish_date'] : date('d.m.Y');
                $publish_time = $data['publish_time'] ? $data['publish_time'] : date('H:i');
                $datetime     = \DateTime::createFromFormat(
                    'd.m.Y H:i', 
                    $publish_date . ' ' .$publish_time
                );

                $article->setTitle($data['title'])
                ->setUri(
                    $repository->getUniqueUri(
                        $friendlyUri->filter($data['title']),
                        $data['locale'],
                        null,
                        $friendlyUri->getDelimiter()
                    )
                )
                ->setLocale($data['locale'])
                ->setImage($data['image'])
                ->setBody($data['body'])
                ->setPublished($datetime)
                ->setAuthor($this->identity());

                if ($category = $this->getObjectManager()
                    ->getRepository('Articles\Entity\Category')
                    ->find((int) $data['category'])
                ) {
                    $article->setCategory($category);
                }

                $article->setStatus(Article::STATUS_ACTIVE);

                if (!$data['hidden']) {
                    $article->setStatus(Article::STATUS_ACTIVE);
                } else {
                    $article->setStatus(Article::STATUS_HIDDEN);
                }

                if ($data['draft']) {
                    $article->setStatus(Article::STATUS_DRAFT);
                }

                if ($article->getStatus() == Article::STATUS_ACTIVE && $article->getPublished()->getTimestamp() > time()) {
                    $article->setStatus(Article::STATUS_SCHEDULED);
                }

                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('New article added')
                );

                // Trigger event
                $event = new Event();
                $event->setTarget($article)
                ->setParam('controller', $this)
                ->setParam('repository', $repository)
                ->setParam('uri', $this->url()->fromRoute('app/article', ['uri' => $article->getUri(), 'locale' => $article->getLocale()], ['force_canonical' => true]))
                ->setParam('entityManager', $this->getObjectManager());

                if ($article->getStatus() == Article::STATUS_DRAFT) {
                    $event->setName(Article::EVENT_ONDRAFT);
                } elseif ($article->getStatus() == Article::STATUS_SCHEDULED) {
                    $event->setName(Article::EVENT_ONSCHEDULED);
                } else {
                    $event->setName(Article::EVENT_ONADD);
                }

                if (!$article->getId()) {
                    $this->getObjectManager()->persist($article);
                }

                $this->getEvent()->getApplication()->getEventManager()->trigger($event);
                $this->getObjectManager()->flush();

                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current()
                    ]
                );  
            }
        }

        if (in_array(
                $request->getQuery('recall'), 
                [self::INSERT_IMAGE_EDITOR, self::INSERT_IMAGE_TITLE]
            ) 
            && !$this->apiCall()->isReturned()
        ) {
            if ($request->isPost()) {
                // prepare form to draft article
                $form->setMode(Article::STATUS_DRAFT);
                $form->setData($request->getPost());
                
                if ($form->isValid()) {
                    if (!isset($article)) {
                        $article = new Article();
                    }
                    $data = $form->getData();
                    

                    $article->setTitle($data['title'])
                    ->setLocale($data['locale'])
                    ->setImage($data['image'])
                    ->setBody($data['body'])
                    ->setAuthor($this->identity())
                    ->setStatus(Article::STATUS_DRAFT);

                    if ($data['publish_date']) {
                        $publish_date = $data['publish_date'] ? $data['publish_date'] : date('d.m.Y');
                        $publish_time = $data['publish_time'] ? $data['publish_time'] : date('H:i');
                        $datetime     = \DateTime::createFromFormat(
                            'd.m.Y H:i', 
                            $publish_date . ' ' .$publish_time
                        );
                        
                        $article->setPublished($datetime);
                    }

                    if ((int) $data['category']) {
                        $category = $this->getObjectManager()
                        ->getRepository('Articles\Entity\Category')
                        ->find((int) $data['category']);
                        
                        if ($category) {
                            $article->setCategory($category);
                        }
                    }

                    if (!$article->getId()) {
                        $this->getObjectManager()->persist($article);
                    }

                    // Trigger event
                    $event = new Event();
                    $event->setName(Article::EVENT_ONDRAFT)
                    ->setTarget($article)
                    ->setParam('controller', $this)
                    ->setParam('repository', $repository)
                    ->setParam('entityManager', $this->getObjectManager());

                    $this->getEventManager()->trigger($event);
                    $this->getObjectManager()->flush();

                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                            'action' => 'add',
                            'id'     => $article->getId(),
                        ],
                        [
                            'query' => [
                                'recall' => $request->getQuery('recall'),
                            ],
                        ]
                    );                    
                }
            } else {
                $parameteres = [
                    'mode' => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGE,
                ];

                switch ($request->getQuery('recall')) {
                    case self::INSERT_IMAGE_TITLE: 
                        $parameteres['thumbnails'] = ['article_title'];
                    break;
                    case self::INSERT_IMAGE_EDITOR: 
                        $parameteres['thumbnails'] = ['article_body'];
                    break;
                }

                return $this->apiCall()->call(
                    $this->url()->fromRoute(
                        'app/admin/images-manager',
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    ),
                    $parameteres
                );                
            }
        }

        return [
            'form'                => $this->formArticles,
            'insertImageToEditor' => self::INSERT_IMAGE_EDITOR, 
            'insertImageToTitle'  => self::INSERT_IMAGE_TITLE, 
            'editorAppendImage'   => isset($append) ? $append : null,
            'article'             => isset($article) ? $article : null,
        ];  
    }

    public function editAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );

        if ($this->params('id') 
            && $article = $repository->find($this->params('id'))
        ) {
            $form = $this->formArticles;
            $form->get('locale')->setValueOptions($this->locale()->all());

            if ($this->apiCall()->isReturned()) {
                if ($request->getQuery('recall')) {
                    $query = $request->getUri()->getQueryAsArray();

                    unset($query['recall']);

                    return $this->redirect()->toUrl(
                        $request->getUri()->setQuery($query)->toString()
                    );
                }
            }

            if (!$request->isPost()
                && in_array(
                    $request->getQuery('recall'), 
                    [self::INSERT_IMAGE_EDITOR, self::INSERT_IMAGE_TITLE]
                )
            ) {
                $parameteres = [
                    'mode' => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGE,
                ];

                switch ($request->getQuery('recall')) {
                    case self::INSERT_IMAGE_TITLE: 
                        $parameteres['thumbnails'] = ['article_title'];
                    break;
                    case self::INSERT_IMAGE_EDITOR: 
                        $parameteres['thumbnails'] = ['article_body'];
                    break;
                }

                return $this->apiCall()->call(
                    $this->url()->fromRoute(
                        'app/admin/images-manager',
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    ),
                    $parameteres
                );
            }

            if ($request->isPost()) {
                // prepare form to draft article
                $form->setMode(Article::STATUS_DRAFT);
                $form->setData($request->getPost());

                if (in_array(
                        $request->getQuery('recall'), 
                        [self::INSERT_IMAGE_EDITOR, self::INSERT_IMAGE_TITLE]
                    )
                    && $form->isValid()
                ) {
                    $data = $form->getData();
                    $article->setTitle($data['title'])
                    ->setLocale($data['locale'])
                    ->setImage($data['image'])
                    ->setBody($data['body'])
                    ->setStatus(Article::STATUS_DRAFT);

                    if ($data['publish_date']) {
                        $publish_date = $data['publish_date'] ? $data['publish_date'] : date('d.m.Y');
                        $publish_time = $data['publish_time'] ? $data['publish_time'] : date('H:i');
                        $datetime     = \DateTime::createFromFormat(
                            'd.m.Y H:i', 
                            $publish_date . ' ' .$publish_time
                        );
                        
                        $article->setPublished($datetime);
                    }

                    if ((int) $data['category']) {
                        $category = $this->getObjectManager()
                        ->getRepository('Articles\Entity\Category')
                        ->find((int) $data['category']);
                        
                        if ($category) {
                            $article->setCategory($category);
                        }
                    }

                    $this->getObjectManager()->flush();

                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                            'action' => 'edit',
                            'id'     => $article->getId(),
                        ],
                        [
                            'query' => [
                                'recall' => $request->getQuery('recall'),
                            ],
                        ]
                    );
                } else {
                    // normal form
                    $form->setMode(null);

                    if ($form->isValid()) {
                        $data         = $form->getData();
                        $friendlyUri  = new FriendlyUri('_');
                        $publish_date = $data['publish_date'] ? $data['publish_date'] : date('d.m.Y');
                        $publish_time = $data['publish_time'] ? $data['publish_time'] : date('H:i');
                        $datetime     = \DateTime::createFromFormat(
                            'd.m.Y H:i', 
                            $publish_date . ' ' .$publish_time
                        );

                        if ($article->getTitle() != $data['title'] || !$article->getUri()) {
                            $article
                                ->setUri(
                                    $repository->getUniqueUri(
                                        $friendlyUri->filter($data['title']),
                                        $data['locale'],
                                        null,
                                        $friendlyUri->getDelimiter()
                                    )
                                );
                        }

                        $article->setTitle($data['title'])
                        ->setLocale($data['locale'])
                        ->setImage($data['image'])
                        ->setBody($data['body'])
                        ->setPublished($datetime);

                        if ($data['category'] && $category = $this->getObjectManager()
                            ->getRepository('Articles\Entity\Category')
                            ->find((int) $data['category'])
                        ) {
                            $article->setCategory($category);
                        } else {
                            $article->setCategory(null);
                        }

                        $article->setStatus(Article::STATUS_ACTIVE);

                        if (!$data['hidden']) {
                            $article->setStatus(Article::STATUS_ACTIVE);
                        } else {
                            $article->setStatus(Article::STATUS_HIDDEN);
                        }

                        if ($data['draft']) {
                            $article->setStatus(Article::STATUS_DRAFT);
                        }

                        $this->flashMessenger()->addSuccessMessage(
                            $this->translate('Changes saved')
                        );

                        // Trigger event
                        $event = new Event();
                        $event->setTarget($article)
                        ->setName(Article::EVENT_ONEDIT)
                        ->setParam('controller', $this)
                        ->setParam('repository', $repository)
                        ->setParam('entityManager', $this->getObjectManager());

                        $this->getEventManager()->trigger($event);
                        $this->getObjectManager()->flush();

                        return $this->redirect()->toRoute(
                            null,
                            [
                                'locale' => $this->locale()->current()
                            ]
                        );
                    }
                }
            } else {
                $form->setData(
                    [
                        'locale'        => $article->getLocale(),
                        'title'         => $article->getTitle(),
                        'image'         => $article->getImage(),
                        'body'          => $article->getBody(),
                        'category'      => $article->getCategory() ? $article->getCategory()->getId() : null,
                        'hidden'        => $article->getStatus() == Article::STATUS_HIDDEN,
                        'publish_date'  => $article->getPublished() ? $article->getPublished()->format('d.m.Y') : null,
                        'publish_time'  => $article->getPublished() ? $article->getPublished()->format('H:i') : null,
                    ]
                );
            }

            if ($this->apiCall()->isReturned()) {
                $recall    = $this->apiCall()
                ->getCallerUri()->getQueryAsArray()['recall'];
                $apiResult = $this->apiCall()->getResult();
                
                switch ($recall) {
                    case self::INSERT_IMAGE_TITLE:
                        $form->get('image')->setValue($apiResult['thumbnails']['article_title'][0]['href']);
                    break; 
                    case self::INSERT_IMAGE_EDITOR: 
                        $append = $apiResult['thumbnails']['article_body'][0]['href'];
                    break;
                }
            }

            return [
                'article'             => $article,
                'form'                => $form,
                'insertImageToEditor' => self::INSERT_IMAGE_EDITOR, 
                'insertImageToTitle'  => self::INSERT_IMAGE_TITLE, 
                'editorAppendImage'   => isset($append) ? $append : null,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return; 
    }

    public function deleteAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Article'
        );

        if ($this->params('id') 
            && $article = $repository->find($this->params('id'))
        ) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($article);

                $this->flashMessenger()->addInfoMessage(
                    $this->translate('Article deleted!')
                );

                // Trigger event
                $event = new Event();
                $event->setName(Article::EVENT_ONDRAFT)
                ->setTarget($article)
                ->setParam('controller', $this)
                ->setParam('repository', $repository)
                ->setParam('entityManager', $this->getObjectManager());

                $this->getEventManager()->trigger($event); 
                
                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );               
            }

            return [
                'article' => $article,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return; 
    }

    public function menuApiAction()
    {
        $request = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if (!(
                $this->params('id') 
                && ($container = $repository->find($this->params('id')))

             )
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->formMenuArticle;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $articles = $this->getObjectManager()->getRepository(
                    'Articles\Entity\Article'
                )
                ->findBy(
                    [
                        'id' => $data['selected']
                    ]
                );

                if (count($articles) == 1) {
                    $page    = new \Menu\Entity\Page();
                    $article = current($articles);

                    $page->setTitle($article->getTitle())
                    ->setLabel($article->getTitle())
                    ->setLocale($article->getLocale())
                    ->setVisible(true)
                    ->setInfo(
                        sprintf(
                            'Article (%s)',
                            $this->locale()->all()[$article->getLocale()]
                        )
                    )
                    ->setRoute('app/article')
                    ->setParams(
                        [
                            'locale' => $article->getLocale(),
                            'uri'    => $article->getUri(),
                            'type'   => 'html'
                        ]
                    );

                    $this->getObjectManager()->persist($page);
                    $container->getPages()->add($page);
                    $this->getObjectManager()->flush();

                    return $this->redirect()->toRoute(
                        'app/admin/menu/page',
                        [
                            'action' => 'edit',
                            'id'     => $page->getId()
                        ],
                        [
                            'query' => [
                                'return' => $this->url()->fromRoute(
                                    'app/admin/menu/container',
                                    [
                                        'action' => 'edit',
                                    ],
                                    true
                                )
                            ]
                        ],
                        true
                    );
                } else {
                    foreach ($articles as $article) {
                        $page = new \Menu\Entity\Page();

                        $page->setTitle($article->getTitle())
                        ->setLabel($article->getTitle())
                        ->setLocale($article->getLocale())
                        ->setVisible(true)
                        ->setActive(true)
                        ->setInfo(
                            sprintf(
                                'Article (%s)',
                                $this->locale()->all()[$article->getLocale()]
                            )
                        )
                        ->setRoute('app/article')
                        ->setParams(
                            [
                                'locale' => $article->getLocale(),
                                'uri'    => $article->getUri(),
                                'type'   => 'html'
                            ]
                        );

                        $this->getObjectManager()->persist($page);
                        $container->getPages()->add($page);
                    }

                    return $this->redirect()->toRoute(
                        'app/admin/menu/container',
                        [
                            'action' => 'edit',
                        ],
                        true
                    );
                }
            }
        }

        return [
            'form'      => $form,
            'container' => $container
        ]; 
    }
}