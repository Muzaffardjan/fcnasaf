<?php 
/**
 * VideosController
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media\Controller;

use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;
use Application\Controller\AbstractObjectManagerAwareController;
use Media\Likes;

class VideosController extends AbstractObjectManagerAwareController
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

    public function videosAction()
    {
        $request    = $this->getRequest(); 
        $repository = $this->getObjectManager()
        ->getRepository('Media\Entity\VideoInfo');
        $videos  = $repository->getPaginated(
            [
                'locale' => $this->locale()->current()
            ]
        );
        $videos  = new Paginator($videos);
        $videos->setCurrentPageNumber($request->getQuery('page', 1))
        ->setItemCountPerPage(12);

        return [
            'videos' => $videos,
        ];
    }

    public function viewAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\VideoInfo'
        );

        if ($this->params('uri')
            && ($info = $repository->findOneBy(['uri' => $this->params('uri')]))
        ) { 
            $video = $info->getVideo();

            $this->layout('media/layout/gallery');

            return [
                'video'   => $video,
                'info'    => $info,
                'liked'   => $this->getLikes()->isLiked($video)
            ]; 
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function likeAction()
    {
        $request    = $this->getRequest();
        $repository = $this->getObjectManager()->getRepository(
            'Media\Entity\Video'
        );

        if ($this->params('id')
            && $request->isXmlHttpRequest()
            && ($video = $repository->find($this->params('id')))
        ) {
            $likes = $this->getLikes();

            if(!$likes->isLiked($video)) {
                $this->setNeedFlush(true);
                $video->setLikes($video->getLikes() + 1);
                $likes->like($video);

                return new JsonModel(
                    [
                        'status' => true,
                        'likes'  => $likes->getVideoLikes()
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