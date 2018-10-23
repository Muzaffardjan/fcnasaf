<?php 
/**
 * Locale helper
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\Stdlib\ArrayUtils;
use Application\Locales\Iso639;

class Locale extends AbstractHelper implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;
    use Iso639;

    /**
     * @var array
     */
    protected $asIso639;

    /**
     * @var string
     */
    protected $inIso639;

    /**
     * Construct 
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, array $config)
    {
        $this->setTranslator($translator);
        $this->translatorConfig = $config['translator'];
    }

    /**
     * Invoke
     *
     * @return Locale
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Returns current locale
     *
     * @return string
     */
    public function current()
    {
        return $this->getTranslator()->getLocale();
    }

    /**
     * Returns current locale in ISO 639 format
     *
     * @return string
     */
    public function currentAsIso639()
    {
        if (null === $this->inIso639) {
            $asIso639 = $this->allAsIso639();
            $current  = $this->current();

            $asIso639 = ArrayUtils::filter(
                $asIso639,
                function ($locale) use ($current) {
                    if (strtolower($locale) == $current || 
                        strtoupper($locale) == $current
                    ) {
                        return true;                        
                    }

                    return false;
                },
                ArrayUtils::ARRAY_FILTER_USE_KEY
            );

            if (empty($asIso639)) {
                throw new \Zend\Stdlib\Exception\RuntimeException(
                    'Unknown locale given.'
                );
            }

            $this->inIso639 = current(array_keys($asIso639));
        }

        return $this->inIso639;
    }

    /**
     * Returns all available locales
     *
     * @return array
     */
    public function all()
    {
        return $this->translatorConfig['locales'];
    }

    /**
     * Returns all avilabel locales in ISO 639 format
     *
     * @return array 
     */
    public function allAsIso639()
    {  
        if (null === $this->asIso639) {
            $asIso639 = ArrayUtils::filter(
                $this->_locales,
                function ($locale) {
                    if (isset($this->translatorConfig['locales'][strtolower($locale)])
                        || isset($this->translatorConfig['locales'][strtoupper($locale)])
                    ) {
                        return true;
                    }

                    return false;
                }
            );

            foreach ($asIso639 as $key => $locale) {
                if (isset($this->translatorConfig['locales'][strtolower($locale)])) {
                    $asIso639[$locale] = $this->translatorConfig['locales'][strtolower($locale)];
                } else {
                    $asIso639[$locale] = $this->translatorConfig['locales'][strtoupper($locale)];
                }

                unset($asIso639[$key]);
            }

            $this->asIso639 = $asIso639;
        }

        return $this->asIso639;
    }
}