<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Articles;

use Articles\Entity\Article;
use Articles\Repository\ArticlesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\EventManager\EventManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Controllers
use Articles\Controller\ArticlesController;
use Articles\Controller\Manage\CategoriesController as ManageCategoriesController;
use Articles\Controller\Manage\ArticlesController as ManageArticlesController;

//Forms 
use Articles\Form\ArticlesForm;
use Articles\Form\CategoriesForm;
use Articles\Form\MenuArticle;
use Articles\Form\MenuCategory;

// Helpers 
use Articles\View\Helper\RelatedHelper;
use Articles\View\Helper\ArticlesHelper;

// Listeners
use Articles\Listener\MenuApiListener;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getServiceConfig()
    {

        return [
            'factories' => [
                'Articles\Form\ArticlesForm' => function ($sm) {
                    $locale = $sm->get('application')
                    ->getMvcEvent()->getRouteMatch()->getParams()['locale'];

                    return new ArticlesForm(
                        null,
                        [
                            'entity_manager' => $sm->get(
                                'Doctrine\ORM\EntityManager'
                            ),
                            'locale'         => $locale,
                            'locales'        => $sm->get('config')['translator']['locales']
                        ]
                    );
                },
                MenuApiListener::class => function () {
                    return new MenuApiListener();
                },
                MenuArticle::class => function ($sm) {
                    return new MenuArticle(
                        $sm->get('Doctrine\ORM\EntityManager')
                    );
                },
                MenuCategory::class => function ($sm) {
                    return new MenuCategory(
                        $sm->get('Doctrine\ORM\EntityManager')
                    );
                },
            ],
        ]; 
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'relatedArticles' => function ($hpm) {
                    return new RelatedHelper(
                        $hpm->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    );
                }, 
                'showArticles' => function ($hpm) {
                    return new ArticlesHelper(
                        $hpm->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    );
                }, 
            ],
        ];  
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                'Articles\Controller\Manage\Categories' => function ($cm) {
                    return new ManageCategoriesController(
                        $cm->getServiceLocator()->get(
                            MenuCategory::class
                        ),
                        $cm->getServiceLocator()->get(
                            \Menu\Form\PagesForm::class
                        )
                    );
                },
                'Articles\Controller\Manage\Articles' => function ($cm) {
                    return new ManageArticlesController(
                        $cm->getServiceLocator()->get(
                            'Articles\Form\ArticlesForm'
                        ),
                        $cm->getServiceLocator()->get(
                            MenuArticle::class
                        )
                    );
                },
                'Articles\Controller\Articles' => function ($cm) {
                    return new ArticlesController();
                }
            ],
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            $e->getApplication()->getServiceManager()
            ->get(MenuApiListener::class)
        );

        try {
            /**
             * @var ObjectManager $objectManager
             * @var ArticlesRepository $articlesR
             */
            $objectManager = $e->getApplication()
                ->getServiceManager()
                ->get('Doctrine\ORM\EntityManager');

            $articlesR = $objectManager->getRepository(Article::class);
            $scheduled = $articlesR->findScheduled();

            if ($scheduled) {
                /**
                 * @var Article $article
                 */
                foreach ($scheduled as $article) {
                    $eventManager->trigger(Article::EVENT_ONADD, $article);
                    $article->setStatus(Article::STATUS_ACTIVE);
                }

                $objectManager->flush();
            }
        } catch (\Exception $e) {
            
        }
    }
}
