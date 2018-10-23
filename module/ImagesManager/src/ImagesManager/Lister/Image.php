<?php 
/**
 * Lister image type
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Lister;

use \SplFileInfo;
use ImagesManager\Thumbnail\Storage\Organizer;

class Image 
{
    /**
     * @var array $config
     */
    private $config = [];

    /**
     * @var SplFileInfo $file
     */
    private $file;

    /**
     * @var string $baseUrl
     */
    private $baseUrl;

    /**
     * Sets options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['config']) && is_array($options['config'])) {
            $this->config = $options['config']; 
        }

        if (isset($options['base_url'])) {
            $this->setBaseUrl($options['base_url'] ? $options['base_url'] : '/');
        }

        if (isset($options['thumbnail_organizer'])) {
            $this->setThumbnailStorageOrganizer($options['thumbnail_organizer']);
        }

        return $this;
    }

    public function setFileInfo(SplFileInfo $file) 
    {
        $this->file = $file;

        return $this;
    }

    public function getFileInfo() 
    {
        return $this->file;
    }

    public function setBaseUrl($baseUrl) 
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function getBaseUrl() 
    {
        if (!$this->baseUrl) {
            throw new \Exception('Base url not set!');
        }

        return $this->baseUrl;
    }

    public function setThumbnailStorageOrganizer(Organizer $storageOrganizer)
    {
        $this->thumbnailOrganizer = $storageOrganizer;
        return $this;
    }

    public function getThumbnailStorageOrganizer()
    {
        return $this->thumbnailOrganizer;
    }

    public function getHref() 
    {
        $href = rtrim($this->getBaseUrl(), '\\/') . '/' .
                ltrim(
                    str_replace(
                        'public/', 
                        '', 
                        str_replace('\\', '/', $this->getFileInfo()->getPathname())
                    ), 
                    '\\/'
                );

        return $this->formatHref($href);
    }
    
    public function getPathname()
    {
        return str_replace("\\", "/", $this->getFileInfo()->getPathname());
    }

    public function getThumbnailHref()
    {
        $href = rtrim($this->getBaseUrl(), '\\/') . '/' .
                ltrim(
                    str_replace(
                        'public/',
                        '',
                        $this->getThumbnailStorageOrganizer()
                        ->getThumbnailPath(
                            $this->getFileInfo()->getPathname(),
                            'list'
                        )
                    ),
                    '\\/'
                );

        return $this->formatHref($href);
    }

    public function formatHref($href)
    {
        $exploded = explode('.', $href);
        $key = count($exploded) - 1;

        if ($key > 0) {
            $exploded[$key] = strtolower($exploded[$key]);

            return str_replace("'", "%27", join('.', $exploded));
        } else {
            throw new \Exception('Image format unsupported or unknown image format');
        }
    }
}