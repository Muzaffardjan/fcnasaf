<?php 
/**
 * Watermarker
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager;

use WebinoImageThumb\Service\ImageThumb;
use PHPThumb\GD as PHPThumb;

class Watermarker
{
    const POSITION_LEFT         = -1;
    const POSITION_RIGHT        = 1;
    const POSITION_TOP          = 1;
    const POSITION_BOTTOM       = -1;
    const POSITION_CENTER       = 0;

    /**
     * @var array   $options
     */
    protected $options;

    /**
     * @var bool    $state
     */
    protected $state = false;

    /**
     * @var string $watermarkPath
     */
    protected $watermarkPath;

    /**
     * @var float   $scale
     */
    protected $scale = 33;

    /**
     * @var float   $xPosition
     */
    protected $xPosition = 0;

    /**
     * @var float   $yPosition
     */
    protected $yPosition = 0;

    /**
     * @var ImageThumb $thumbnailer
     */
    protected $thumbnailer;

    /**
     * @var PHPThumb   $thumb
     */
    protected $thumb;

    /**
     * Construct 
     *
     * @param ImageThumb $thumbnailer
     * @param null|array $options
     */
    public function __construct(ImageThumb $thumbnailer, array $options = null)
    {
        $this->thumbnailer = $thumbnailer;

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns status
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getState();
    }

    /**
     * Gets watermark thumb
     *
     * @return PHPThumb 
     */
    public function getWatermarkThumb()
    {   
        if (!$this->isEnabled()) {
            throw new Exception\WatermarkingIsDisbaledException();
        }

        $thumb = $this->thumbnailer->create($this->getWatermarkImagePath(), [], []);

        if (!$this->thumb instanceof PHPThumb) {
            $this->thumb = $this->thumbnailer->createWatermark(
                $thumb, 
                [$this->getXPosition(), $this->getYPosition()], 
                $this->getScale() / 100
            );
        }

        return $this->thumb;
    }

    /**
     * Sets watermarker options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        if (isset($options['watermark_use'])) {
            $this->setState($options['watermark_use']);
        }

        if (isset($options['watermark_path'])) {
            $this->setWatermarkImagePath($options['watermark_path']);
        }

        if (isset($options['scale'])) {
            $this->setScale($options['scale']);
        }

        if (isset($options['x_position'])) {
            $this->setXPosition($options['x_position']);
        }

        if (isset($options['y_position'])) {
            $this->setYPosition($options['y_position']);
        }
    }

    /**
     * Sets path to watermark image
     *
     * @throws Exception\ExceptionInterface
     * @param string $pathname
     */
    public function setWatermarkImagePath($pathname)
    {
        if (!is_string($pathname)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Method expects string, \'%s\' given.',
                    gettype($pathname)
                )
            );
        }

        if (!is_file($pathname)) {
            throw new Exception\InvalidFilepathException(
                'Watermark image not found in given path'
            );
        }

        $this->watermarkPath = realpath($pathname);
    }

    /**
     * Gets path to watermark image
     *
     * @throws Exception\ExceptionInterface
     * @return string
     */
    public function getWatermarkImagePath()
    {
        if ($this->getState() 
            && null === $this->watermarkPath 
            || !is_file($this->watermarkPath)
        ) {
            throw new Exception\InvalidFilepathException(
                'Path to watermark image is invalid.'
            );
        }

        return $this->watermarkPath;
    }

    /**
     * Gets watermarker options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets watermark scale to image
     *
     * @param float $scale
     */
    public function setScale($scale)
    {
        $this->scale = (float) $scale;        
    }

    /**
     * Gets watermark scale to image
     *
     * @return float
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Sets X position of waterkmark
     *
     * @param float $position
     */
    public function setXPosition($position)
    {
        $this->xPosition = (int) $position;
    }

    /**
     * Gets X position of watermark
     *
     * @return float $position
     */
    public function getXPosition()
    {
        return $this->xPosition;
    }

    /**
     * Sets Y position of waterkmark
     *
     * @param float $position
     */
    public function setYPosition($position)
    {
        $this->yPosition = (int) $position;
    }

    /**
     * Gets Y position of watermark
     *
     * @return float $position
     */
    public function getYPosition()
    {
        return $this->yPosition;
    }

    /**
     * Enables/Disbales waterkmark use
     *
     * @param bool $use
     */
    protected function setState($use)
    {
        $this->state = (bool) $use;
    }

    /**
     * Gets watermark use state
     *
     * @return bool
     */
    protected function getState()
    {
        return $this->state;
    }
}