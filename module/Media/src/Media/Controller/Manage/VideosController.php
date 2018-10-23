<?php 
/**
 * VideosController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Controller\Manage;

use Admin\Controller\AbstractObjectManagerAwareController;
use Zend\Paginator\Paginator;
use Application\Filter\FriendlyUri;
use Media\Form\VideosForm;
use Media\Form\VideosInfoForm;
use Media\Entity\Video;
use Media\Entity\VideoInfo;

class VideosController extends AbstractObjectManagerAwareController
{
    public function indexAction()
    {
        $videos = $this->getObjectManager()->getRepository(
            'Media\Entity\Video'
        )->getPaginated();

        $videos = new Paginator($videos);

        $videos->setItemCountPerPage(9)
        ->setCurrentPageNumber($this->getRequest()->getQuery('page'));

        return [
            'videos' => $videos
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = new VideosForm();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $video = new Video();
                $data  = $form->getData();

                $video->setPoster($data['poster'])
                ->setDate(\DateTime::createFromFormat('d.m.Y', $data['date']))
                ->setSource($data['source'])
                ->setLikes(0);

                $this->getObjectManager()->persist($video);

                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current()
                    ]
                );
            }
        }

        if ($request->getQuery('poster')) {
            if (!$this->apiCall()->isReturned() 
                && !$this->apiCall()->isCancelled()
            ) {
                return $this->apiCall()->call(
                    $this->url()->fromRoute(
                        'app/admin/images-manager', 
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    ),
                    [
                        'mode'      => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGE,
                        'thumbnails'=> ['video_poster'],
                    ]
                );
            }

            if ($this->apiCall()->isReturned()) {
                $result = $this->apiCall()->getResult();

                return $this->redirect()->toRoute(
                    null,
                    [],
                    [
                        'query' => [
                            'poster_image' => $result['thumbnails']['video_poster'][0]['href']
                        ],
                    ],
                    true
                );
            }
        }

        if ($request->getQuery('poster_image')) {
            $form->get('poster')->setValue(
                $request->getQuery('poster_image')
            );
        }

        return [
            'form' => $form,
        ];
    }

    public function addInfoAction()
    {
        $request = $this->getRequest();
        $videos  = $this->getObjectManager()->getRepository(
            'Media\Entity\Video'
        );

        if ($request->isPost() 
            && $this->params('id')
            && ($video = $videos->find($this->params('id')))
        ) {
            $form    = new VideosInfoForm();
            $locales = $this->locale()->all();

            foreach ($video->getInfo() as $info) {
                if (isset($locales[$info->getLocale()])) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $form->get('locale')->setValueOptions($locales);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $videosInfo= $this->getObjectManager()->getRepository(
                    'Media\Entity\VideoInfo'
                );
                $videoInfo = new VideoInfo();
                $data      = $form->getData();
                $filter    = new FriendlyUri('_');

                $videoInfo->setLocale($data['locale'])
                ->setTitle($data['title'])
                ->setVideo($video)
                ->setUri(
                    $videosInfo->getUniqueUri(
                        $filter->filter($videoInfo->getTitle()),
                        $videoInfo->getLocale(),
                        true,
                        $filter->getDelimiter()
                    )
                );

                $this->flashMessenger()->addInfoMessage(
                    $this->translate('Video info was added successfully')
                );

                $this->getObjectManager()->persist($videoInfo);

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'edit',
                    ],
                    true
                );
            } else {
                return $this->forward()->dispatch(
                    'Media\Controller\Manage\Videos',
                    [
                        'locale'   => $this->locale()->current(),
                        'action'   => 'edit',
                        'id'       => $video->getId(),
                        'formInfo' => $form,
                    ]
                )->setTemplate('media/manage/videos/edit');
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\Video'
        );

        if ($this->params('id') 
            && $video = $repository->find($this->params('id'))
        ) {
            $form     = new VideosForm();
            $formInfo = $this->params('formInfo') ?: new VideosInfoForm();
            $locales  = $this->locale()->all();

            foreach ($video->getInfo() as $info) {
                if (isset($locales[$info->getLocale()])) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $formInfo->get('locale')->setValueOptions($locales);

            if ($request->isPost() && !$this->params('formInfo')) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();
                    $video->setPoster($data['poster'])
                    ->setDate(\DateTime::createFromFormat('d.m.Y', $data['date']))
                    ->setSource($data['source']);
                    
                    return $this->redirect()->toRoute(
                        null, 
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
                }
            } else {
                $form->setData(
                    [
                        'poster' => $video->getPoster(),
                        'date'   => $video->getDate()->format('d.m.Y'),
                        'source' => $video->getSource(),
                    ]
                );
            }

            if ($request->getQuery('poster')) {
                if (!$this->apiCall()->isReturned() 
                    && !$this->apiCall()->isCancelled()
                ) {
                    return $this->apiCall()->call(
                        $this->url()->fromRoute(
                            'app/admin/images-manager', 
                            [
                                'locale' => $this->locale()->current(),
                            ]
                        ),
                        [
                            'mode'      => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGE,
                            'thumbnails'=> ['video_poster'],
                        ]
                    );
                }

                if ($this->apiCall()->isReturned()) {
                    $result = $this->apiCall()->getResult();

                    return $this->redirect()->toRoute(
                        null,
                        [],
                        [
                            'query' => [
                                'poster_image' => $result['thumbnails']['video_poster'][0]['href']
                            ],
                        ],
                        true
                    );
                }
            }

            if ($request->getQuery('poster_image')) {
                $form->get('poster')->setValue($request->getQuery('poster_image'));
            }

            return [
                'video'    => $video,
                'form'     => $form,
                'formInfo' => $formInfo,
                'locales'  => $locales,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editInfoAction()
    {   
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\VideoInfo'
        );

        if ($this->params('id')
            && $videoInfo = $repository->find($this->params('id'))
        ) {
            $form = new VideosInfoForm(null, ['use_locale' => false]);

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $filter = new FriendlyUri('_');
                    $data   = $form->getData();

                    $videoInfo->setTitle($data['title'])
                    ->setUri(
                        $repository->getUniqueUri(
                            $filter->filter($videoInfo->getTitle()),
                            $videoInfo->getLocale(),
                            true,
                            $filter->getDelimiter(),
                            $videoInfo
                        )
                    );
                    
                    return $this->redirect()
                    ->toRoute(
                        null,
                        [
                            'action' => 'edit',
                            'id'     => $videoInfo->getVideo()->getId()
                        ],
                        true
                    );
                }
            } else {
                $form->get('title')->setValue($videoInfo->getTitle());
            }

            return [
                'info'    => $videoInfo,
                'referer' => $this->url()->fromRoute(
                    null, 
                    [
                        'action' => 'edit', 
                        'id' => $videoInfo->getVideo()->getId()
                    ], 
                    true
                ),
                'form'  => $form
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\Video'
        );

        if ($this->params('id')
            && $video = $repository->find($this->params('id'))
        ) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($video);

                return $this->redirect()->toRoute(
                    null,
                    [
                        'locale' => $this->locale()->current()
                    ]
                );
            }

            return [
                'video'    => $video
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteInfoAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\VideoInfo'
        );

        if ($this->params('id')
            && $videoInfo = $repository->find($this->params('id'))
        ) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($videoInfo);

                $this->flashMessenger()->addWarningMessage(
                    $this->translate('Video info was deleted')
                );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'edit',
                        'id'     => $videoInfo->getVideo()->getId(),
                    ],
                    true
                );
            }

            return [
                'info'    => $videoInfo,
                'referer' => $this->url()->fromRoute(null, ['action' => 'edit', 'id' => $videoInfo->getVideo()->getId()], true)
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}