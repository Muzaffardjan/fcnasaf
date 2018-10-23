<?php 
/**
 * Thumbnails storage organizer
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace ImagesManager\Thumbnail\Storage;

use ImagesManager\Thumbnail\Thumbnailer;

class Organizer 
{
    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var string $storageRoot
     */
    protected $storageRoot;

    /**
     * @var array $thumbnails
     */
    protected $thumbnails = [];

    /**
     * Construct
     *
     * @param array $config Images manager module's config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * Sets config
     *
     * @throws  ImagesManager\Exception\ExceptionInterface
     * @param   array $config
     * @return  Organizer
     */
    public function setConfig(array $config)
    {
        if (!isset($config['images_directory'])) {
            throw new Exception\InvalidConfigException(
                'Config must have \'images_directory\' key!'
            );
        } else {
            $this->setStorageRoot($config['images_directory']);
        }

        if (isset($config['thumbnails'])) {
            $this->thumbnails = $config['thumbnails'];
        }

        $this->config = $config;

        return $this;
    }

    /**
     * Gets config
     *
     * @return array
     */
    public function getConfig()
    {  
        return $this->config;
    }

    /**
     * Sets storage root
     *
     * @param   string      $storagePath
     * @return  Organizer
     */
    public function setStorageRoot($storagePath)
    {
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $this->storageRoot = rtrim($storagePath, '\\/');
        return $this;
    }

    /**
     * Gets storage root path
     *
     * @return string
     */
    public function getStorageRoot()
    {
        return $this->storageRoot;
    }

    /**
     * Returns where to save thumbnail
     *
     * @param   string              $sourceImagePath
     * @param   string|Thumbnailer  $thumbnailer
     * @param   null|string         $format
     * @return  string 
     */
    public function getThumbnailPath(
        $sourceImagePath, 
        $thumbnailer, 
        $format = null
    ) {
        $thumbnailStorage = '';
        $savePath         = '';
        $savePath        .= $this->getThumbnailsDirectoryPathname(
            $thumbnailer
        );
        $savePath        .= substr(
            $sourceImagePath, 
            strpos(
                $sourceImagePath, 
                $this->getStorageRoot()
            ) + strlen($this->getStorageRoot())
        );

        if (null !== $format) {
            $savePath  = substr($savePath, 0, strrpos($savePath, '.') + 1);
            $savePath .= $format;
        }

        return $savePath;
    }

    /**
     * Returns thumnail storage directory path
     *
     * @param   string|Thumbnailer  $thumbnailer
     * @return  string 
     */
    public function getThumbnailsDirectoryPathname($thumbnailer)
    {
        if ($thumbnailer instanceof Thumbnailer) {
            $thumbnailStorage = $thumbnailer->getDirectorypath();
        } elseif (is_string($thumbnailer) 
                  && isset($this->thumbnails[$thumbnailer])
        ) {
            $thumbnailStorage = $this->thumbnails[$thumbnailer]['directory'];
        } else {
            throw new \ImagesManager\Exception\InvalidArgumentException();
        }

        $pathname  = $this->getStorageRoot();
        $pathname .= '/';
        $pathname .= ltrim($thumbnailStorage, '\\/');

        return $pathname;
    }
}