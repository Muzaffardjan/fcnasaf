<?php 
/**
 * PhotoGalleryController admin side
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Controller\Manage;

use DateTime;
use Media\Repository\PhotoGalleriesRepository;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Admin\Controller\AbstractObjectManagerAwareController;
use Application\Filter\FriendlyUri;
use Media\Form\PhotoGalleriesForm;
use Media\Form\PhotoGalleriesInfoForm;
use Media\Form\PhotosForm;
use Media\Form\PhotosInfoForm;
use Media\Entity\PhotoGallery;
use Media\Entity\PhotoGalleryInfo;
use Media\Entity\Photo;
use Media\Entity\PhotoInfo;

class PhotoGalleryController extends AbstractObjectManagerAwareController 
{
    /**
     * Shows all photo galleries and basic managing tools to them
     */
    public function indexAction()
    {
        /**
         * @var PhotoGalleriesRepository $repository
         */
        $repository = $this->getObjectManager()->getRepository('Media\Entity\PhotoGallery');

        /**
         * @var Paginator $galleries
         */
        $galleries = new Paginator($repository->getPaginated());

        $this->layout()->tag = [
            'body' => [
                'class' => ['page-gallery']
            ],
        ];

        $galleries->setItemCountPerPage(10);

        $galleries->setCurrentPageNumber(
            $this->getRequest()->getQuery('page', 1)
        );

        return new ViewModel([
            'galleries' => $galleries,
        ]);
    }

    public function addAction()
    {
        $form    = new PhotoGalleriesForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data         = $form->getData();
                $photoGallery = new PhotoGallery();

                $photoGallery->setCreatedDate(
                    DateTime::createFromFormat('d.m.Y', $data['created_date'])
                )
                ->setStatus(PhotoGallery::STATUS_DRAFT);

                $this->getObjectManager()->persist($photoGallery);
                $this->getObjectManager()->flush();
                
                return $this->redirect()->toRoute(
                    null, 
                    [
                        'locale' => $this->locale()->current(),
                        'action' => 'addDescription',
                        'id'     => $photoGallery->getId(),
                    ]
                );
            }
        }

        return [
            'form' => $form,
        ];
    }

    public function addDescriptionAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            $form    = new PhotoGalleriesInfoForm();
            $locales = $this->locale()->all();

            foreach ($photoGallery->getInfo() as $info) {
                if (isset($locales[$info->getLocale()])) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $form->get('locale')->setValueOptions($locales);

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data           = $form->getData();
                    $info           = new PhotoGalleryInfo();
                    $filter         = new FriendlyUri('_');
                    $infoRepository = $this->getObjectManager()->getRepository(
                        'Media\Entity\PhotoGalleryInfo'
                    );

                    $info->setLocale($data['locale'])
                    ->setTitle($data['title'])
                    ->setUri(
                        $infoRepository->getUniqueUri(
                            $filter->filter($data['title'])
                        ),
                        $data['locale'],
                        true,
                        $filter->getDelimiter(),
                        $info
                    );

                    $photoGallery->getInfo()->add($info);
                    $info->setGallery($photoGallery);

                    $this->getObjectManager()->persist($info);
                    
                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ],
                        true
                    );
                }
            }

            return [
                'photoGallery' => $photoGallery,
                'form'         => $form,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addPhotosStepAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            $thumbnails = [
                'photo'           => 'gallery_photo', 
                'thumbnail'       => 'gallery_front',
                'thumbnail_small' => 'gallery_small',
            ];

            $flag = $this->apiCall()->isCancelled();

            if (!$this->apiCall()->isReturned() && !$flag) {
                return $this->apiCall()->call(
                    $this->url()->fromRoute(
                        'app/admin/images-manager', 
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    ),
                    [
                        'mode' => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGES,
                        'thumbnails' => $thumbnails,
                    ]
                ); 
            } elseif ($flag) {
                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'deleteGallery',
                    ],
                    [
                        'query' => ['confirm' => true]
                    ],
                    true
                );   
            } else {
                $result        = $this->apiCall()->getResult();
                $objectManager = $this->getObjectManager();

                if (!$request->getQuery('add')) {
                    $photoGallery->getPhotos()->clear();
                }

                foreach ($result['thumbnails'][$thumbnails['photo']] as $key => $value) {
                    $photo = new Photo();

                    $photo->setSource($value['href'])
                    ->setShotDate($photoGallery->getCreatedDate())
                    ->setGallery($photoGallery)
                    ->setThumbnail($result['thumbnails'][$thumbnails['thumbnail']][$key]['href'])
                    ->setSmallThumbnail($result['thumbnails'][$thumbnails['thumbnail_small']][$key]['href'])
                    ->setLikes(0);

                    $photoGallery->getPhotos()->add($photo);
                }

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'editPhotosStep',
                    ],
                    true
                );  
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addPhotoInfoModalAction()
    {
        $request = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            "Media\Entity\Photo"
        );

        if (
            $this->params('id')
            && ($photo = $repository->find($this->params('id')))
            && $request->isXmlHttpRequest()
        ) {
            $form = new PhotosInfoForm();
            $locales = $this->locale()->all();

            foreach ($photo->getInfo() as $info) {
                if (isset($locales[$info->getLocale()])) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $form->get('locale')->setValueOptions($locales);

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $photoInfo = new PhotoInfo();
                    $data      = $form->getData();

                    $photoInfo->setTitle($data['title'])
                    ->setLocale($data['locale'])
                    ->setPhoto($photo);

                    $this->getObjectManager()->persist($photoInfo);

                    return new JsonModel(
                        [
                            'status'          => true,
                            'removeAddButton' => (count($locales) == 1),
                        ]
                    );
                }
            }

            $view = new ViewModel (
                [
                    'photo'    => $photo,
                    'form'     => $form,
                    'locales'  => $locales,
                ]
            ); 

            $view->setTerminal(true);
            return $view;
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addPhotosToGalleryAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            $thumbnails = [
                'photo'           => 'gallery_photo', 
                'thumbnail'       => 'gallery_front',
                'thumbnail_small' => 'gallery_small',
            ];

            $flag = $this->apiCall()->isCancelled();

            if (!$this->apiCall()->isReturned() && !$flag) {
                return $this->apiCall()->call(
                    $this->url()->fromRoute(
                        'app/admin/images-manager', 
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    ),
                    [
                        'mode' => \ImagesManager\Controller\ManagerController::LISTER_MODE_SELECT_IMAGES,
                        'thumbnails' => $thumbnails,
                    ]
                ); 
            } elseif ($flag) {
                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'deleteGallery',
                    ],
                    [
                        'query' => ['confirm' => true]
                    ],
                    true
                );   
            } else {
                $result        = $this->apiCall()->getResult();
                $objectManager = $this->getObjectManager();

                foreach ($result['thumbnails'][$thumbnails['photo']] as $key => $value) {
                    $photo = new Photo();

                    $photo->setSource($value['href'])
                    ->setShotDate($photoGallery->getCreatedDate())
                    ->setGallery($photoGallery)
                    ->setThumbnail($result['thumbnails'][$thumbnails['thumbnail']][$key]['href'])
                    ->setSmallThumbnail($result['thumbnails'][$thumbnails['thumbnail_small']][$key]['href'])
                    ->setLikes(0);

                    $photoGallery->getPhotos()->add($photo);
                }

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'editGallery',
                    ],
                    true
                );  
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function finishCreatingGalleryAction() 
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            $photoGallery->setStatus(PhotoGallery::STATUS_FINISHED);

            return $this->redirect()->toRoute(
                null,
                [
                    'locale' => $this->locale()->current()
                ]
            );
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editGalleryAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') 
            && $photoGallery = $repository->find($this->params('id'))
        ) {
            $form    = new PhotoGalleriesForm();
            if ($this->params('infoForm')) {
                $infoForm = $this->params('infoForm');
            } else {
                $infoForm = new PhotoGalleriesInfoForm();
            }
            $locales = $this->locale()->all();

            foreach ($photoGallery->getInfo() as $info) {
                if ($locales[$info->getLocale()]) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $infoForm->get('locale')->setValueOptions($locales);

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();



                    /**
                     * @var PhotoGallery $photoGallery
                     */
                    $photoGallery->setCreatedDate(DateTime::createFromFormat('d.m.Y', $data['created_date']))
                    ->setStatus(PhotoGallery::STATUS_FINISHED);

                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current()
                        ]
                    );
                }
            } else {
                $form->get('created_date')->setValue(
                    $photoGallery->getCreatedDate()->format('d.m.Y')
                );
            }

            return [
                'photoGallery'  => $photoGallery,
                'form'          => $form,
                'locales'       => $locales,
                'infoForm'      => $infoForm,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addGalleryInfoAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );
        $prg        = $this->prg(
            $request->getUri()->toString(),
            true
        );
        $infoForm   = new PhotoGalleriesInfoForm();

        if ($this->params('id') 
            && ($photoGallery = $repository->find($this->params('id')))
        ) {
            $locales = $this->locale()->all();

            foreach ($photoGallery->getInfo() as $info) {
                if ($locales[$info->getLocale()]) {
                    unset($locales[$info->getLocale()]);
                }
            }

            $infoForm->get('locale')->setValueOptions($locales);
            if ($request->isPost()) {
                $infoForm->setData($request->getPost());

                if ($infoForm->isValid()) {
                    $data             = $infoForm->getData();
                    $photoGalleryInfo = new PhotoGalleryInfo();
                    $filter           = new FriendlyUri('_');
                    $infoRepository = $this->getObjectManager()->getRepository(
                        'Media\Entity\PhotoGalleryInfo'
                    );

                    $photoGalleryInfo->setLocale($data['locale'])
                    ->setTitle($data['title'])
                    ->setUri(
                        $infoRepository->getUniqueUri(
                            $filter->filter($data['title'])
                        ),
                        $data['locale'],
                        true,
                        $filter->getDelimiter(),
                        $info
                    )->setGallery($photoGallery);

                    $this->getObjectManager()->persist($photoGalleryInfo);

                    return $this->redirect()->toRoute(
                        null,
                        [
                            'action' => 'editGallery',
                            'id'     => $photoGallery->getId(),
                        ],
                        true
                    );
                } else {
                    return $prg;
                }
            }
        } 

        if (is_array($prg)) {
            $infoForm->setData($prg)->isValid();
            $view = $this->forward()->dispatch(
                'Media\Controller\Manage\PhotoGallery',
                [
                    'locale'    => $this->locale()->current(),
                    'action'    => 'editGallery',
                    'id'        => $photoGallery->getId(),
                    'infoForm'  => $infoForm 
                ]
            );

            $view->setTemplate('media/manage/photo-gallery/edit-gallery');

            return $view;
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editPhotosStepAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            if ($request->isXmlHttpRequest()) {
                
            }

            return [
                'photoGallery' => $photoGallery,
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editPhotoAction()
    {
        $request = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            "Media\Entity\Photo"
        );

        if ($this->params('id') && $photo = $repository->find($this->params('id'))) {
            if ($request->getQuery('return')) {
                $referer = \Zend\Uri\UriFactory::factory(
                    $request->getQuery('return')
                );
            }

            $form = new PhotosForm();
            $locales = $this->locale()->all();

            foreach ($photo->getInfo() as $info) {
                if (isset($locales[$info->getLocale()])) {
                    unset($locales[$info->getLocale()]);
                }
            }

            if ($request->isPost()) {
                
            } else {
                $form->get('shot_date')->setValue(
                    $photo->getShotDate() ? $photo->getShotDate()->format('d.m.Y') : date('d.m.Y') 
                );
            }

            return [
                'photo'    => $photo,
                'form'     => $form,
                'locales'  => $locales,
                'referer'  => isset($referer) ? $referer : null,
                'return'   => $request->getQuery('return'),
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editPhotoInfoModalAction()
    {
        $request = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            "Media\Entity\PhotoInfo"
        );

        if ($this->params('id') 
            && ($photoInfo = $repository->find($this->params('id')))
            && $request->isXmlHttpRequest()
        ) {
            $form   = new PhotosInfoForm(null, ['use_locale' => false]);

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data      = $form->getData();

                    $photoInfo->setTitle($data['title']);

                    return new JsonModel(
                        [
                            'status'          => true,
                        ]
                    );
                }
            } else {
                $form->get('title')->setValue($photoInfo->getTitle());
            }

            $view = new ViewModel (
                [
                    'photoInfo'=> $photoInfo,
                    'form'     => $form
                ]
            ); 

            $view->setTerminal(true);
            return $view;
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editGalleryStepAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {
            $form    = new PhotoGalleriesForm();
            $form->get('created_date')->setValue(
                $photoGallery->getCreatedDate()->format('d.m.Y')
            );

            if ($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data         = $form->getData();

                    $photoGallery->setCreatedDate(
                        DateTime::createFromFormat('d.m.Y', $data['created_date'])
                    );
                    
                    return $this->redirect()->toRoute(
                        null, 
                        [
                            'locale' => $this->locale()->current(),
                            'action' => 'addDescription',
                            'id'     => $photoGallery->getId(),
                        ]
                    );
                }
            }

            return [
                'photoGallery' => $photoGallery,
                'form'         => $form,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function editGalleryDescriptionStepAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGalleryInfo'
        );

        if ($this->params('id') 
            && $photoGalleryInfo = $repository->find($this->params('id'))
        ) {
            $form  = new PhotoGalleriesInfoForm(null, ['use_locale' => false]);

            if ($request->isPost() || $request->isXmlHttpRequest()) {
                if ($request->isPost()) {
                    $form->setData($request->getPost());

                    if ($form->isValid()) {
                        $data = $form->getData();

                        $photoGalleryInfo->setTitle($data['title']);

                        return new JsonModel(
                            [
                                'status'    => true,
                                'redirect'  => $this->url()->fromRoute(
                                    null, 
                                    [
                                        'action' => 'addDescription',
                                        'id'     => $photoGalleryInfo
                                        ->getGallery()
                                        ->getId(),
                                    ], 
                                    true
                                )
                            ]
                        );
                    }
                } else {
                    $form->setData(
                        [
                            'title' => $photoGalleryInfo->getTitle()
                        ]
                    );
                }

                $view = new ViewModel(
                    [
                        'form'             => $form,
                        'photoGalleryInfo' => $photoGalleryInfo,
                    ]
                );

                $view->setTerminal(true);

                return $view; 
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteGalleryAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGallery'
        );

        if ($this->params('id') && $photoGallery = $repository->find($this->params('id'))) {

            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($photoGallery);

                return $this->redirect()->toRoute(
                    null, 
                    [
                        'locale' => $this->locale()->current(),
                    ]
                );  
            }

            if ($request->getHeaders('Referer')) {
                $referer = \Zend\Uri\UriFactory::factory(
                    $request->getHeaders('Referer')->getFieldValue()
                );

                $default = $this->url()->fromRoute(
                    'app/admin/media/gallery', 
                    ['locale' => $this->locale()->current()]
                );

                if (strpos($referer->toString(), $default) === false) {
                    $referer = $default;
                }
            }

            return [
                'photoGallery' => $photoGallery,
                'referer'      => isset($referer) ? $referer : null,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deleteGalleryInfoAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoGalleryInfo'
        );

        if ($this->params('id') && $photoGalleryInfo = $repository->find($this->params('id'))) {
            if ($request->getQuery('confirm')) {
                $this->getObjectManager()->remove($photoGalleryInfo);

                if ($request->getQuery('return')) {
                    return $this->redirect()->toUrl(
                        $request->getQuery('return')
                    );
                } else {
                    return $this->redirect()->toRoute(
                        null,
                        [
                            'locale' => $this->locale()->current(),
                        ]
                    );
                }
            }

            return [
                'galleryInfo' => $photoGalleryInfo,
            ];
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deletePhotoAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\Photo'
        );

        if ($this->params('id') && $photo = $repository->find($this->params('id'))) {
            if ($request->getQuery('confirm') || $request->isXmlHttpRequest()) {
                $this->getObjectManager()->remove($photo);

                if (!$request->isXmlHttpRequest() && $request->getQuery('return')) {
                    return $this->redirect()->toUrl(
                        $request->getQuery('return')
                    );
                } else {
                    return new JsonModel(
                        [
                            'status' => true,
                        ]
                    );
                }
            }

            return [
                'photo' => $photo,
            ];  
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function deletePhotoInfoAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\PhotoInfo'
        );

        if ($this->params('id') && $photoInfo = $repository->find($this->params('id'))) {
            $this->getObjectManager()->remove($photoInfo);

            $referer = \Zend\Uri\UriFactory::factory(
                $request->getHeaders('Referer')->getFieldValue()
            );
            
            return $this->redirect()->toRoute(
                null,
                [
                    'locale' => $this->locale()->current(),
                    'action' => 'editPhoto',
                    'id'     => $photoInfo->getPhoto()->getId()
                ],
                [
                    'query' => $referer->getQueryAsArray()
                ]
            );
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}