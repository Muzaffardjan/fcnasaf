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
use Media\Form\MenuGalleryForm;
use Menu\Entity\Page;

class PhotoGalleryMenuApiController extends AbstractObjectManagerAwareController
{
    /**
     * @var PagesForm
     */
    protected $formMenuPages;

    /**
     * @var MenuGalleryForm
     */
    protected $formMenuGallery;

    public function __construct(PagesForm $formMenuPages, MenuGalleryForm $formMenuGallery)
    {
        $this->formMenuPages = $formMenuPages;
        $this->formMenuGallery = $formMenuGallery;
    }

    public function galleriesAction()
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
                        'Galleries (%s)',
                        $this->locale()->all()[$data['locale']]
                    )
                )
                ->setRoute('app/media/galleries')
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
                    'label'   => $this->translate('Gallery'),
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

    public function galleryAction()
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

        $form = $this->formMenuGallery;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data      = $form->getData();
                $galleries = $this->getObjectManager()
                ->getRepository('Media\Entity\PhotoGalleryInfo')
                ->findBy(
                    [
                        'id' => $data['selected']
                    ]
                );

                foreach ($galleries as $gallery) {
                    $page = new Page();

                    $page->setLabel($gallery->getTitle())
                    ->setTitle($gallery->getTitle())
                    ->setLocale($gallery->getLocale())
                    ->setInfo(
                        sprintf('Gallery alias(%s)', $gallery->getTitle())
                    )
                    ->setRoute('app/media/gallery')
                    ->setParams(
                        [
                            'locale' => $gallery->getLocale(),
                            'uri'    => $gallery->getUri(),
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