<?php 
/**
 * GalleriesController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Controller;

use Media\Entity\PhotoGalleryInfo;
use Media\Repository\PhotoGalleryInfoRepository;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;
use Application\Controller\AbstractObjectManagerAwareController;
use Media\Likes;
use Zend\View\Model\ViewModel;

class GalleriesController extends AbstractObjectManagerAwareController
{
    /**
     * @var Likes
     */
    protected $likes;

    public function __construct(Likes $likes)
    {
        $this->likes = $likes;
        // Read only controller
        $this->setNeedFlush(false);
    }

    /**
     * Gets the value of likes.
     *
     * @return Likes
     */
    public function getLikes()
    {
        return $this->likes;
    }

    public function galleriesAction()
    {
        $request    = $this->getRequest(); 
        $repository = $this->getObjectManager()
        ->getRepository('Media\Entity\PhotoGalleryInfo');
        $galleries  = $repository->getPaginated(
            ['locale' => $this->locale()->current()],
            true
        );
        $galleries  = new Paginator($galleries);
        $galleries->setCurrentPageNumber($request->getQuery('page', 1))
        ->setItemCountPerPage(12);

        return [
            'galleries' => $galleries,
        ];
    }

    public function viewAction()
    {
        /**
         * @var Response $response
         */
        $response = $this->getResponse();

        /**
         * @var PhotoGalleryInfoRepository $repository
         */
        $repository = $this->getObjectManager()->getRepository(PhotoGalleryInfo::class);

        /**
         * @var PhotoGalleryInfo $info
         */
        $info = $repository->findOneBy(['uri' => $this->params('uri')]);

        if ($info) {
            $gallery = $info->getGallery();

            return new ViewModel([
                'info' => $info,
                'gallery' => $gallery,
                'likes' => $this->getLikes()->getGalleryLikes($gallery)
            ]);
        }

        $response->setStatusCode(404);
        return;
    }

    public function likeAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\Photo'
        );

        if ($this->params('id')
            && $request->isXmlHttpRequest()
            && ($photo = $repository->find($this->params('id')))
        ) {
            $likes = $this->getLikes();

            if(!$likes->isLiked($photo)) {
                $this->setNeedFlush(true);
                $photo->setLikes($photo->getLikes() + 1);
                $likes->like($photo);

                return new JsonModel(
                    [
                        'status' => true,
                        'likes'  => $likes->getPhotoLikes()
                    ]
                );
            } else {
                return new JsonModel(
                    [
                        'status'  => true,
                        'message' => 'Already liked!',
                    ]
                );
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}