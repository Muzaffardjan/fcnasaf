<?php 
/**
 * Thumbnailer
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Thumbnail;

use WebinoImageThumb\Service\ImageThumb;
use ImagesManager\Exception as Exception;
use ImagesManager\Watermarker;
use PHPThumb\GD as PHPThumb;
use ImagesManager\Thumbnail\Storage\Organizer as StorageOrganizer;
use ImagesManager\Thumbnail\Cropping\AdapterInterface as CroppingAdapterInterface;

class Thumbnailer 
{
    const FORMAT_GIF = 'GIF';
    const FORMAT_JPG = 'JPG';
    const FORMAT_PNG = 'PNG';

    const RATIO_ERROR_MARGIN = 0.01;

    const DIMENSION_AUTO = 'auto';

    /**
     * @var array $options
     */
    protected $options = [];

    /**
     * @var string $directory
     */
    protected $directory;

    /**
     * @var float $width
     */
    protected $width = 'auto';

    /**
     * @var float $height
     */
    protected $height = 'auto';

    /**
     * @var int $quality
     */
    protected $quality = 60;

    /**
     * @var int $autoMode
     */
    protected $autoMode = true;

    /**
     * @var bool $watermarkUse
     */
    protected $watermarkUse = true;

    /**
     * @var WebinoImageThumbnailer $webino
     */
    protected $webino;

    /**
     * @var StorageOrganizer $storageOrganizer
     */
    protected $storageOrganizer;

    /**
     * @var float $ratioErrorMargin
     */
    protected $ratioErrorMargin;

    /**
     * Construct
     *
     * @param ImageThumb $webino
     * @param null|array $options
     */
    public function __construct(ImageThumb $webino, array $options = null)
    {
        /**
         * @var WebinoImageThumbnailer
         */
        $this->webino = $webino;

        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Creates thumbnail and saves it to target dir
     *
     * @param   string                   $imagepath
     * @param   CroppingAdapterInterface $cropAdapter
     *          If setted creates thumbnail by adapter parametres
     * @param   string                   $format
     *          Target image format (default is given images)
     * @return  mixed                                
     *          Returns saved path of thumbnail
     *          if auto mode disabled it will return false
     *          when given image ratio different than thumbnailers
     */
    public function createThumbnail(
        $imagepath, 
        CroppingAdapterInterface $cropAdapter = null,
        $format = null
    )
    {
        if (!is_file($imagepath)) {
            throw new Exception\InvalidFilepathException(
                'Image not found.'
            );
        }

        $tempname = rtrim(dirname($imagepath), "\\/") .'/tmp_'. basename($imagepath);
        $options  = [];
        $plugins  = [];

        // Set jpg image quality
        if ($this->getQuality()) {
            $options['jpegQuality'] = $this->getQuality();
        }

        if ($this->getWatermarkUse() 
            && $this->getWatermarker() instanceof Watermarker
            && $this->getWatermarker()->isEnabled()
        ) {
            $plugins[] = $this->getWatermarker()->getWatermarkThumb();
        }

        $image = $this->webino->create($imagepath, $options, $plugins);

        if ($this->getOptions()['width'] == self::DIMENSION_AUTO) {
            $this->setWidth((int)$image->getCurrentDimensions()['width']);
        }

        if ($this->getOptions()['height'] == self::DIMENSION_AUTO) {
            $this->setHeight((int)$image->getCurrentDimensions()['height']);
        }

        if (null !== $cropAdapter) {
            if ($cropAdapter->getRotateDegree()) {
                $image->rotateImageNDegrees($cropAdapter->getRotateDegree());
            }

            $image->crop(
                $cropAdapter->getOffsetX(),
                $cropAdapter->getOffsetY(),
                $cropAdapter->getCropWidth(),
                $cropAdapter->getCropHeight()
            );
        } elseif (!$this->getAutoMode() && 
            !$this->isSameRatio($image->getCurrentDimensions())
        ) {
            return false;
        }

        $this->adaptiveStrictResize($image);
        
        if (null === $format) {
            $iFormat = substr($imagepath, strrpos($imagepath, '.') + 1);
            $format  = strtoupper($iFormat);

            if ($format === 'JPEG') {
                $format = 'JPG';
            }
        } elseif ($format && in_array($format, ['GIF', 'JPG', 'PNG'])) {
            throw new Exception\InvalidArgumentException('Invalid format.');
        }

        $savePath = $this->getStorageOrganizer()->getThumbnailPath(
            $imagepath,
            $this,
            strtolower($format)
        );
        $savePath = str_replace('\\', '/', $savePath);

        if (!is_dir($directoryPathname = dirname($savePath))) {
            mkdir($directoryPathname, 0777, true);
        }

        if (is_file($tempname)) {
            unlink($tempname);
        }

        if (is_file($savePath)) {
            unlink($savePath);
        }

        // Fix imperfections of PhpThumb
        ob_start();
        $image->show($savePath, $format);
        $rawImage = ob_get_clean();

        file_put_contents($savePath, $rawImage);
        unset($rawImage);
        return $savePath;
    }

    /**
     * Sets options of class
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (isset($options['directory'])) {
            $this->setDirectorypath($options['directory']);
        }

        if (isset($options['width'])) {
            $this->setWidth($options['width']);
        }

        if (isset($options['height'])) {
            $this->setHeight($options['height']);
        }

        if (isset($options['quality'])) {
            $this->setQuality($options['quality']);
        }

        if (isset($options['auto_mode'])) {
            $this->setAutoMode($options['auto_mode']);
        }

        if (isset($options['watermark'])) {
            $this->setWatermarkUse($options['watermark']);
        }

        if (isset($options['watermarker'])) {
            $this->setWatermarker($options['watermarker']);
        }

        if (isset($options['storage_organizer'])) {
            $this->setStorageOrganizer($options['storage_organizer']);
        }

        $this->options = $options;
    }

    /**
     * Gets options
     *
     * @return null|array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets thumbnails directory
     *
     * @throws  Exception\ExceptionInterface
     * @param   string $pathname
     * @return  Thumbnailer
     */
    public function setDirectorypath($pathname)
    {
        $this->directory = $pathname;

        return $this;
    }

    /**
     * Gets thumbnails directory
     *
     * @throws Exception\ExceptionInterface
     * @return string
     */
    public function getDirectorypath()
    {
        if (null === $this->directory) {
            throw new Exception\UndefinedOptionException();
        }

        return $this->directory;
    }

    /**
     * Sets width of target thumbnail
     *
     * @throws  Exception\ExceptionInterface
     * @param   float $width
     * @return  Thumbnailer
     */
    public function setWidth($width)
    {
        if (0 > (float) $width) {
            throw new Exception\InvalidArgumentException(
                'Width can not be a negative number'
            );
        }

        $this->width = (float) $width;

        return $this;
    }

    /**
     * Gets width of target thumbnail
     *
     * @return float 
     */
    public function getWidth()
    {   
        if (null === $this->width) {
            throw new Exception\UndefinedOptionException();
        }

        return $this->width;
    }

    /**
     * Sets height of target thumbnail
     *
     * @throws  Exception\ExceptionInterface
     * @param   float $height
     * @return  Thumbnailer
     */
    public function setHeight($height)
    {
        if (0 > (float) $height) {
            throw new Exception\InvalidArgumentException(
                'Height can not be a negative number'
            );
        }

        $this->height = (float) $height;

        return $this;
    }

    /**
     * Gets height of target thumbnail
     *
     * @return float 
     */
    public function getHeight()
    {   
        if (null === $this->height) {
            throw new Exception\UndefinedOptionException();
        }

        return $this->height;
    }

    /**
     * Sets quality of thumbnail image
     *
     * @param   int $quality
     * @return  Thumbnailer
     */
    public function setQuality($quality)
    {
        $this->quality = (int) $quality; 

        return $this;
    }

    /**
     * Gets target quality of thumbnail image
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Sets automode state
     *
     * @throws  Exception\ExceptionInterface
     * @param   bool $enable
     * @return  Thumbnailer
     */
    public function setAutoMode($enable)
    {
        if (!is_bool($enable)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->autoMode = (bool) $enable;

        return $this;
    }

    /**
     * Gets automode state
     *
     * @return bool
     */
    public function getAutoMode()
    {
        return $this->autoMode;
    }

    /**
     * Sets watermark use state
     *
     * @throws  Exception\ExceptionInterface
     * @param   bool $enable
     * @return  Thumbnailers
     */
    public function setWatermarkUse($enable)
    {
        if (!is_bool($enable)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->watermarkUse = (bool) $enable;

        return $this;
    }

    /**
     * Gets watermark use state
     *
     * @return bool
     */
    public function getWatermarkUse()
    {
        return $this->watermarkUse;
    }

    /**
     * Sets watermarker
     *
     * @param   Watermarker $watermarker
     * @return  Thumbnailers
     */
    public function setWatermarker($watermarker)
    {
        $this->watermarker = $watermarker;

        return $this;
    }

    /**
     * Sets storage organizer
     *
     * @param   StorageOrganizer $organizer
     * @return  Thumbnailer     
     */
    public function setStorageOrganizer(StorageOrganizer $organizer)
    {
        $this->storageOrganizer = $organizer;

        return $this;
    }

    /**
     * Gets storage organizer
     *
     * @return StorageOrganizer
     */
    public function getStorageOrganizer()
    {
        if (null === $this->storageOrganizer) {
            throw new Exception\UndefinedOptionException(
                'Storage organizer must be set.'
            );
        }

        return $this->storageOrganizer;
    }

    /**
     * Gets watermarker
     *
     * @throws  Exception\ExceptionInterface
     * @return  Watermarker
     */
    public function getWatermarker()
    {
        if (null === $this->watermarker) {
            throw new Exception\UndefinedOptionException();
        }

        return $this->watermarker;
    }

    /**
     * Gets thumbnail path
     *
     * @param   string              $sourceImagePath
     * @param   null|string         $format
     * @return  string
     */
    public function getThumbnailPath($sourceImagePath, $format = null)
    {
        return $this->getStorageOrganizer()->getThumbnailPath(
            $image, 
            $this, 
            $format
        );
    }

    /**
     * Sets ratio error margin
     *
     * @param   float       $margin
     * @return  Thumbnailer
     */
    public function setRatioErrorMargin(float $margin)
    {
        $this->ratioErrorMargin = (float) $margin;

        return $this;
    }

    /**
     * Gets ratio error margin
     *
     * @return float 
     */
    public function getRatioErrorMargin()
    {
        if (is_float($this->ratioErrorMargin)) {
            return $this->ratioErrorMargin;
        }

        return self::RATIO_ERROR_MARGIN;
    }

    /**
     * Maximal adaptive resize but resulted image dimensions strictly equal to
     * desired thumbnail size
     *
     * @param   PHPThumb $image
     * @return  PHPThumb
     */
    protected function adaptiveStrictResize(PHPThumb $image)
    {
        // Scale up small images
        $this->zoomSmallImage($image);
        // Do adaptive resize
        $image->adaptiveResize($this->getWidth(), $this->getHeight());
        // Scale up after just to be shure
        $this->zoomSmallImage($image);
        // Final crop
        $image->cropFromCenter($this->getWidth(), $this->getHeight());

        return $image;
    }

    /**
     * Adaptively zooms image to given dimensions
     *
     * @param   PHPThumb  $image
     * @return  PHPThumb
     */
    protected function zoomSmallImage($image)
    {
        $res          = $image->getCurrentDimensions();
        $targetWidth  = $this->getWidth();
        $targetHeight = $this->getHeight();

        if ($res['width'] < $targetWidth || 
            $res['height'] < $targetHeight
        ) {
            $percent = 100;
            $widthR  = ($targetWidth * 100) / $res['width'];
            $heightR = ($targetHeight * 100) / $res['height'];

            if ($widthR >= $heightR) {
                $percent = $widthR;
            } else {
                $percent = $heightR;
            }

            $image->resizePercent($percent);
        }

        return $image;
    }

    /**
     * Compares image ratio with thumbnail's
     * Returns true if image ratio equal to thumbnail's
     *
     * @param   array    $imageDimension [width, height]
     * @return  bool 
     */
    protected function isSameRatio(array $imageDimension)
    {
        $ratio  = [
            'a'  => $imageDimension['height'] / $imageDimension['width'],
            'b'  => $this->getHeight() / $this->getWidth(),
        ];
        
        if(abs($ratio['a'] - $ratio['b']) < $this->getRatioErrorMargin())
        {
            return true;
        }

        return false;
    }
}