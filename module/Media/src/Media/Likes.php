<?php 
/**
 * Likes
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Media;

use Zend\Http\Header\SetCookie;
use Zend\Http\Cookies;
use Zend\Http\Response;
use Zend\Http\Request;
use Media\Entity\Photo;
use Media\Entity\PhotoGallery;
use Media\Entity\Video;
use Media\Exception\InvalidArgumentException;

class Likes 
{   
    const COOKIE_KEY_PHOTO_LIKES = 'media_photo_likes';
    const COOKIE_KEY_VIDEO_LIKES = 'media_video_likes';
    const COOKIE_EXPIRE_TIME     = 31536000;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $photoLikes;

    /**
     * @var array
     */
    protected $videoLikes;

    /**
     * Construct
     */
    public function __construct(Request $request, Response $response) 
    {
        $this->setRequest($request)
        ->setResponse($response);
    }

    /**
     * Gets the value of request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the value of request.
     *
     * @param Request $request the request
     *
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Gets the value of response.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the value of response.
     *
     * @param Response $response the response
     *
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Gets likes of given gallery
     *
     * @param   PhotoGallery $gallery
     * @return  array
     */
    public function getGalleryLikes(PhotoGallery $gallery)
    {
        $photos = $gallery->getPhotos();
        $result = [];

        if ($likes = $this->getPhotoLikes()) {
            $ids = [];

            foreach ($photos as $photo) {
                $ids[] = $photo->getId();
            }

            $result = array_intersect($ids, $likes);

            sort($result, SORT_NUMERIC);
        }

        return $result;
    }

    /**
     * Gets photo likes
     * 
     * @return array
     */
    public function getPhotoLikes()
    {
        if (null === $this->photoLikes) {
            $cookies          = $this->getRequest()->getCookie();
            $this->photoLikes = [];

            if ($cookies 
                && $cookies->offsetExists(self::COOKIE_KEY_PHOTO_LIKES)
            ) {
                $this->photoLikes = explode(
                    ',', 
                    $cookies->offsetGet(
                        self::COOKIE_KEY_PHOTO_LIKES
                    )
                );

                if (!$this->photoLikes 
                    && $cookies->offsetGet(self::COOKIE_KEY_PHOTO_LIKES)
                ) {
                    $this->photoLikes = [(int) $cookies->offsetGet(self::COOKIE_KEY_PHOTO_LIKES)];
                }
            }
        }

        return $this->photoLikes;
    }

    /**
     * Sets photo likes
     *
     * @param   array $likes
     * @return  self
     */
    public function setPhotoLikes(array $likes)
    {
        $this->photoLikes = $likes;
        
        $this->getResponse()
        ->getHeaders()
        ->addHeader(
            new SetCookie(
                self::COOKIE_KEY_PHOTO_LIKES,
                join(',', $this->photoLikes),
                time() + self::COOKIE_EXPIRE_TIME,
                '/',
                null, 
                null,
                true
            )
        );

        return $this;
    }

    /**
     * Gets video likes
     * 
     * @return array
     */
    public function getVideoLikes()
    {
        if (null === $this->videoLikes) {
            $cookies          = $this->getRequest()->getCookie();
            $this->videoLikes = [];

            if ($cookies
                && $cookies->offsetExists(self::COOKIE_KEY_VIDEO_LIKES)
            ) {
                $this->videoLikes = explode(
                    ',', 
                    $cookies->offsetGet(
                        self::COOKIE_KEY_VIDEO_LIKES
                    )
                );

                if (!$this->photoLikes 
                    && $cookies->offsetGet(self::COOKIE_KEY_VIDEO_LIKES)
                ) {
                    $this->photoLikes = [(int) $cookies->offsetGet(self::COOKIE_KEY_VIDEO_LIKES)];
                }
            }
        }

        return $this->videoLikes;
    }

    /**
     * Sets video likes
     *
     * @param   array $likes
     * @return  self
     */
    public function setVideoLikes(array $likes)
    {
        $this->videoLikes = $likes;

        $this->getResponse()
        ->getHeaders()
        ->addHeader(
            new SetCookie(
                self::COOKIE_KEY_VIDEO_LIKES,
                join(',', $this->videoLikes),
                time() + self::COOKIE_EXPIRE_TIME,
                '/',
                null, 
                null,
                true
            )
        );

        return $this;
    }

    /**
     * Returns if any entity is liked
     *
     * @throws  InvalidArgumentException
     * @param   Photo|Video $entity
     * @return  bool
     */
    public function isLiked($entity) 
    {
        if ($entity instanceof Photo) {
            return $this->isLikedPhoto($entity->getId());
        }

        if ($entity instanceof Video) {
            return $this->isLikedVideo($entity->getId());
        }

        throw new InvalidArgumentException(
            sprintf(
                "'Video' or 'Photo' expected '%s' given.",
                gettype($entity) == 'object' ? 
                get_class($entity) :
                gettype($entity)
            )
        );
    }

    /**
     * Is given photo liked by user
     *
     * @param   int   $id
     * @return  bool
     */
    public function isLikedPhoto($id)
    {
        $likes = $this->getPhotoLikes();

        if ($likes) {
            return in_array($id, $likes);
        }

        return false;
    }

    /**
     * Is given video liked by user
     *
     * @param   int   $id
     * @return  bool
     */
    public function isLikedVideo($id)
    {
        $likes = $this->getVideoLikes();

        if ($likes) {
            return in_array($id, $likes);
        }

        return false;
    }

    /**
     * Adds like
     *
     * @throws  InvalidArgumentException
     * @param   Photo|Video $entity
     */
    public function like($entity) 
    {
        if ($entity instanceof Photo) {
            $this->likePhoto($entity->getId());
            return;
        }

        if ($entity instanceof Video) {
            $this->likeVideo($entity->getId());
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                "'Video' or 'Photo' expected '%s' given.",
                gettype($entity) == 'object' ? 
                get_class($entity) :
                gettype($entity)
            )
        );
    }

    /**
     * Adds photo like
     *
     * @param int $id Photo id
     */
    public function likePhoto($id)
    {
        $likes   = $this->getPhotoLikes();
        $likes[] = (string) $id;

        $this->setPhotoLikes($likes);
    }

    /**
     * Adds video like
     *
     * @param int $id Video id
     */
    public function likeVideo($id)
    {
        $likes   = $this->getVideoLikes();
        $likes[] = (string) $id;

        $this->setVideoLikes($likes);
    }
}