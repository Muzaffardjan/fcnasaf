<?php 
/**
 * Generates friendly uri
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Application\Filter;

use Zend\Filter\AbstractFilter;

/**
 * Class FriendlyUri
 * @package Application\Filter
 */
class FriendlyUri extends AbstractFilter
{
    /**
     * @var string
     */
    protected $delimiter = '-';

    /**
     * @var string
     */
    protected $limit;

    /**
     * @var string
     */
    protected $lowercase = true;

    /**
     * @var string
     */
    protected $replacements = [];

    /**
     * @var bool
     */
    protected $transliterate = true;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        $delimiter     = '-', 
        $limit         = null, 
        $lowercase     = true, 
        $replacements  = [], 
        $transliterate = true
    ) {
        $this->setDelimiter($delimiter)
        ->setLimit($limit)
        ->setLowercase($lowercase)
        ->setReplacements($replacements)
        ->setTransliterate($transliterate);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return $this->getUriString($value);
    }

    /**
     * Gets the value of delimiter.
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Sets the value of delimiter.
     *
     * @param string $delimiter the delimiter
     *
     * @return self
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Gets the value of limit.
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the value of limit.
     *
     * @param string $limit the limit
     *
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Gets the value of lowercase.
     *
     * @return string
     */
    public function getLowercase()
    {
        return $this->lowercase;
    }

    /**
     * Sets the value of lowercase.
     *
     * @param string $lowercase the lowercase
     *
     * @return self
     */
    public function setLowercase($lowercase)
    {
        $this->lowercase = $lowercase;

        return $this;
    }

    /**
     * Gets the value of replacements.
     *
     * @return string
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * Sets the value of replacements.
     *
     * @param string $replacements the replacements
     *
     * @return self
     */
    public function setReplacements($replacements)
    {
        $this->replacements = $replacements;

        return $this;
    }

    /**
     * Gets the value of transliterate.
     *
     * @return bool
     */
    public function getTransliterate()
    {
        return $this->transliterate;
    }

    /**
     * Sets the value of transliterate.
     *
     * @param bool $transliterate the transliterate
     *
     * @return self
     */
    public function setTransliterate($transliterate)
    {
        $this->transliterate = $transliterate;

        return $this;
    }

    /**
     * @param $value
     * @return mixed|string
     */
    protected function getUriString($value)
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $value    = mb_convert_encoding((string)$value, 'UTF-8', mb_list_encodings());
        $char_map = array
        (
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z',
            // Uzbek
            "Ў"=>"O", "ў"=>"o", "Ғ"=>"G", "ғ"=>"g", "Ҳ"=>"H", "ҳ"=>"h", "Қ"=>"Q", "қ"=>"q",
        );
        // Make custom replacements
        $value = preg_replace(array_keys($this->getReplacements()), $this->getReplacements(), $value);
        // Transliterate characters to ASCII
        if ($this->getTransliterate()) {
            $value = str_replace(array_keys($char_map), $char_map, $value);
        }
        // Replace non-alphanumeric characters with our delimiter
        $value = preg_replace('/[^\p{L}\p{Nd}]+/u', $this->getDelimiter(), $value);
        // Remove duplicate delimiters
        $value = preg_replace('/(' . preg_quote($this->getDelimiter(), '/') . '){2,}/', '$1', $value);
        // Truncate slug to max. characters
        $value = mb_substr($value, 0, ($this->getLimit() ? $this->getLimit() : mb_strlen($value, 'UTF-8')), 'UTF-8');
        // Remove delimiter from ends
        $value = trim($value, $this->getDelimiter());
        
        return $this->getLowercase() ? mb_strtolower($value, 'UTF-8') : $value;
    }
}