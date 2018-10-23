<?php 
/**
 * PhotoGalleryMenuApiController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Controller\Manage;

use Admin\Controller\AbstractObjectManagerAwareController;
use Menu\Form\PagesForm;
use Menu\Entity\Page;
use Media\Form\MenuVideoForm;

class VideosMenuApiController extends AbstractObjectManagerAwareController
{
    /**
     * @var PagesForm
     */
    protected $formMenuPages;

    /**
     * @var MenuVideoForm
     */
    protected $formMenuVideo;

    public function __construct(PagesForm $formMenuPages, MenuVideoForm $formMenuVideo)
    {
        $this->formMenuPages = $formMenuPages;
        $this->formMenuVideo = $formMenuVideo;
    }

    public function videosAction()
    {
        $request = $this->getRequest();
        $containers = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if (!($this->params('id') 
            && ($container = $containers->find($this->params('id'))))
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->formMenuPages;

        $form->getInputFilter()->get('locale')->setRequired(true);

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $page = new Page();
                $data = $form->getData();

                $page->setLocale($data['locale'])
                ->setLabel($data['label'])
                ->setVisible($data['visible'])
                ->setTarget($data['target'])
                ->setFragment($data['fragment'])
                ->setActive(true)
                ->setInfo(
                    sprintf(
                        'Videos (%s)',
                        $this->locale()->all()[$data['locale']]
                    )
                )
                ->setRoute('app/media/videos')
                ->setParams(
                    [
                        'locale' => $data['locale'],
                    ]
                );

                $this->getObjectManager()->persist($page);
                $container->getPages()->add($page);

                return $this->redirect()->toRoute(
                    'app/admin/menu/container',
                    [
                        'action' => 'edit',
                        'id'     => $container->getId(),
                    ],
                    true
                );
            }
        } else {
            $form->setData(
                [
                    'label'   => $this->translate('Videos'),
                    'visible' => true,
                ]
            );
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

    public function videoAction()
    {
        $request = $this->getRequest();
        $containers = $this->getObjectManager()->getRepository(
            'Menu\Entity\Container'
        );

        if (!($this->params('id') 
            && ($container = $containers->find($this->params('id'))))
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->formMenuVideo;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data      = $form->getData();
                $videos = $this->getObjectManager()
                ->getRepository('Media\Entity\VideoInfo')
                ->findBy(
                    [
                        'id' => $data['selected']
                    ]
                );

                foreach ($videos as $video) {
                    $page = new Page();

                    $page->setLabel($video->getTitle())
                    ->setTitle($video->getTitle())
                    ->setLocale($video->getLocale())
                    ->setInfo(
                        sprintf('Video alias(%s)', $video->getTitle())
                    )
                    ->setRoute('app/media/video')
                    ->setParams(
                        [
                            'locale' => $video->getLocale(),
                            'uri'    => $video->getUri(),
                        ]
                    )
                    ->setActive(true)
                    ->setVisible(true);

                    $this->getObjectManager()->persist($page);
                    $container->getPages()->add($page);
                }

                return $this->redirect()->toRoute(
                    'app/admin/menu/container',
                    [
                        'action' => 'edit',
                        'id'     => $container->getId(),
                    ],
                    true
                );
            }
        }

        return [
            'form'      => $form,
            'container' => $container,
        ];    
    }
}