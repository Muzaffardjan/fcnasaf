<?php 
/**
 * Translate plugin for controller
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\I18n\Translator\TranslatorInterface;

class Translate extends AbstractPlugin
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Construct 
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }    

    /**
     * Invoke
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function __invoke($message, $textDomain = 'default', $locale = null)
    {
        return $this->translate(
            $message, 
            $textDomain, 
            $locale
        );
    }

    /**
     * Translate a message.
     *
     * @param  string $message
     * @param  string $textDomain
     * @param  string $locale
     * @return string
     */
    public function translate($message, $textDomain = 'default', $locale = null)
    {
        return $this->translator->translate(
            $message, 
            $textDomain, 
            $locale
        );
    }
}