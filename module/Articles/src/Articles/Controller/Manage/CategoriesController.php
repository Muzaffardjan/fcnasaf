<?php 
/**
 * Categories manage controller
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Articles\Controller\Manage;

use Application\Filter\FriendlyUri;
use Admin\Controller\AbstractObjectManagerAwareController;
use Articles\Form\CategoriesForm;
use Articles\Entity\Category;
use Menu\Form\PagesForm;
use Articles\Form\MenuCategory;

class CategoriesController extends AbstractObjectManagerAwareController
{
    /**
     * @var MenuCategory
     */
    protected $formMenuCategory;

    /**
     * @var PagesForm
     */
    protected $formPages;

    public function __construct(
        MenuCategory $formMenuCategory,
        PagesForm $formPages
    ) {
        $this->formMenuCategory = $formMenuCategory;
        $this->formPages        = $formPages;
    }

    public function indexAction()
    {
        $this->layout()->tag = [
            'body' => [
                'class' => ['app-documents'],
            ],
        ];

        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Category'
        );

        return [
            'categories' => $repository->findAllDesc(),
        ];
    }

    public function addAction()
    {
        $form    = new CategoriesForm();
        $request = $this->getRequest();

        $form->get('locale')->setValueOptions($this->locale()->all());

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $repository = $this->getObjectManager()->getRepository(
                    'Articles\Entity\Category'
                );
                $filter     = new FriendlyUri('_');
                $data       = $form->getData();
                $uri        = $repository->getUniqueUri(
                    $filter($data['title']),
                    $data['locale'],
                    false,
                    $filter->getDelimiter()
                );
                $category   = new Category();

                $category
                ->setLocale($data['locale'])
                ->setTitle($data['title'])
                ->setUri($uri);

                $this->getObjectManager()->persist($category);
                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );  
            }
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Category'
        );
        $request    = $this->getRequest();
        $category   = $repository->findOneBy(
            [
                'uri'    => $this->params('id'), 
                'locale' => $this->params('targetLocale')
            ]
        );

        if ($category) {
            $form    = new CategoriesForm();

            $form->get('locale')->setValueOptions($this->locale()->all());

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data       = $form->getData();

                    $category
                    ->setLocale($data['locale'])
                    ->setTitle($data['title']);

                    if ($data['title'] != $category->getTitle()) {
                        $filter     = new FriendlyUri('_');
                        $uri        = $repository->getUniqueUri($filter($data['title']));
                        $category->setUri($uri);
                    }

                    $this->flashMessenger()->addInfoMessage(
                        $this->translate('Category updated')
                    );

                    return $this->redirect()->toRoute(
                        'app/admin/articles/categories',
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );  
                }
            } else {
                $form->setData(
                    [
                        'title'  => $category->getTitle(),
                        'locale' => $category->getLocale(),
                    ]
                );
            }

            return [
                'category'  => $category,
                'form'      => $form,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteAction()
    {
        $repository = $this->getObjectManager()->getRepository(
            'Articles\Entity\Category'
        );
        $request    = $this->getRequest();
        $category   = $repository->findOneBy(
            [
                'uri'    => $this->params('id'), 
                'locale' => $this->params('targetLocale')
            ]
        );

        if ($category) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($category);

                $this->flashMessenger()->addInfoMessage(
                    $this->translate('Category successfully deleted')
                );

                return $this->redirect()->toRoute(
                    'app/admin/articles/categories',
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );  
            }

            return [
                'category' => $category,
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

        $form = $this->formMenuCategory;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $categories = $this->getObjectManager()->getRepository(
                    'Articles\Entity\Category'
                )
                ->findBy(
                    [
                        'id' => $data['selected']
                    ]
                );

                if (count($categories) == 1) {
                    $page     = new \Menu\Entity\Page();
                    $category = current($categories);

                    $page->setTitle($category->getTitle())
                    ->setLabel($category->getTitle())
                    ->setLocale($category->getLocale())
                    ->setVisible(true)
                    ->setActive(true)
                    ->setInfo(
                        sprintf(
                            '%s category alias(%s)',
                            $category->getTitle(),
                            $this->locale()->all()[$category->getLocale()]
                        )
                    )
                    ->setRoute('app/category')
                    ->setParams(
                        [
                            'locale' => $category->getLocale(),
                            'uri'    => $category->getUri(),
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
                    foreach ($categories as $category) {
                        $page = new \Menu\Entity\Page();

                        $page->setTitle($category->getTitle())
                        ->setLabel($category->getTitle())
                        ->setLocale($category->getLocale())
                        ->setVisible(true)
                        ->setInfo(
                            sprintf(
                                '%s category alias(%s)',
                                $category->getTitle(),
                                $this->locale()->all()[$category->getLocale()]
                            )
                        )
                        ->setRoute('app/category')
                        ->setParams(
                            [
                                'locale' => $category->getLocale(),
                                'uri'    => $category->getUri(),
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

    public function menuApiBlogAction()
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

        $form = $this->formPages;

        $form->getInputFilter()->get('locale')->setRequired(true);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $page = new \Menu\Entity\Page();

                $page->setTitle($data['title'])
                ->setLabel($data['label'])
                ->setLocale($data['locale'])
                ->setVisible($data['visible'])
                ->setActive(true)
                ->setInfo(
                    sprintf(
                        '%s blog', 
                        $this->locale()->all()[$data['locale']]
                    )
                )
                ->setRoute('app/articles')
                ->setParams(
                    [
                        'locale' => $data['locale'],
                        'type'   => 'html'
                    ]
                );

                $this->getObjectManager()->persist($page);
                $container->getPages()->add($page);

                return $this->redirect()->toRoute(
                    'app/admin/menu/container',
                    [
                        'action' => 'edit',
                    ],
                    true
                );
            }
        } else {
            $form->get('visible')->setValue(true);
        }

        return [
            'form'      => $form,
            'container' => $container,
            'return'    => $this->url()->fromRoute(
                'app/admin/menu/container',
                [
                    'action' => 'edit',
                ],
                true
            )
        ]; 
    }
}