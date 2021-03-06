<?php 
/**
 * Trait to be implemented by locale targeted classes
 * 
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. ("http"    => //www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Application\Locales;

trait Iso639
{
    /**
     * @var array
     */
    protected $_locales         = [
        "af-NA",
        "af-ZA",
        "af",
        "ak-GH",
        "ak",
        "sq-AL",
        "sq",
        "am-ET",
        "am",
        "ar-DZ",
        "ar-BH",
        "ar-EG",
        "ar-IQ",
        "ar-JO",
        "ar-KW",
        "ar-LB",
        "ar-LY",
        "ar-MA",
        "ar-OM",
        "ar-QA",
        "ar-SA",
        "ar-SD",
        "ar-SY",
        "ar-TN",
        "ar-AE",
        "ar-YE",
        "ar",
        "hy-AM",
        "hy",
        "as-IN",
        "as",
        "asa-TZ",
        "asa",
        "az-Cyrl",
        "az-Cyrl-AZ",
        "az-Latn",
        "az-Latn-AZ",
        "az",
        "bm-ML",
        "bm",
        "eu-ES",
        "eu",
        "be-BY",
        "be",
        "bem-ZM",
        "bem",
        "bez-TZ",
        "bez",
        "bn-BD",
        "bn-IN",
        "bn",
        "bs-BA",
        "bs",
        "bg-BG",
        "bg",
        "my-MM",
        "my",
        "ca-ES",
        "ca",
        "tzm-Latn",
        "tzm-Latn-MA",
        "tzm",
        "chr-US",
        "chr",
        "cgg-UG",
        "cgg",
        "zh-Hans",
        "zh-Hans-CN",
        "zh-Hans-HK",
        "zh-Hans-MO",
        "zh-Hans-SG",
        "zh-Hant",
        "zh-Hant-HK",
        "zh-Hant-MO",
        "zh-Hant-TW",
        "zh",
        "kw-GB",
        "kw",
        "hr-HR",
        "hr",
        "cs-CZ",
        "cs",
        "da-DK",
        "da",
        "nl-BE",
        "nl-NL",
        "nl",
        "ebu-KE",
        "ebu",
        "en-AS",
        "en-AU",
        "en-BE",
        "en-BZ",
        "en-BW",
        "en-CA",
        "en-GU",
        "en-HK",
        "en-IN",
        "en-IE",
        "en-JM",
        "en-MT",
        "en-MH",
        "en-MU",
        "en-NA",
        "en-NZ",
        "en-MP",
        "en-PK",
        "en-PH",
        "en-SG",
        "en-ZA",
        "en-TT",
        "en-UM",
        "en-VI",
        "en-GB",
        "en-US",
        "en-ZW",
        "en",
        "eo",
        "et-EE",
        "et",
        "ee-GH",
        "ee-TG",
        "ee",
        "fo-FO",
        "fo",
        "fil-PH",
        "fil",
        "fi-FI",
        "fi",
        "fr-BE",
        "fr-BJ",
        "fr-BF",
        "fr-BI",
        "fr-CM",
        "fr-CA",
        "fr-CF",
        "fr-TD",
        "fr-KM",
        "fr-CG",
        "fr-CD",
        "fr-CI",
        "fr-DJ",
        "fr-GQ",
        "fr-FR",
        "fr-GA",
        "fr-GP",
        "fr-GN",
        "fr-LU",
        "fr-MG",
        "fr-ML",
        "fr-MQ",
        "fr-MC",
        "fr-NE",
        "fr-RW",
        "fr-RE",
        "fr-BL",
        "fr-MF",
        "fr-SN",
        "fr-CH",
        "fr-TG",
        "fr",
        "ff-SN",
        "ff",
        "gl-ES",
        "gl",
        "lg-UG",
        "lg",
        "ka-GE",
        "ka",
        "de-AT",
        "de-BE",
        "de-DE",
        "de-LI",
        "de-LU",
        "de-CH",
        "de",
        "el-CY",
        "el-GR",
        "el",
        "gu-IN",
        "gu",
        "guz-KE",
        "guz",
        "ha-Latn",
        "ha-Latn-GH",
        "ha-Latn-NE",
        "ha-Latn-NG",
        "ha",
        "haw-US",
        "haw",
        "he-IL",
        "he",
        "hi-IN",
        "hi",
        "hu-HU",
        "hu",
        "is-IS",
        "is",
        "ig-NG",
        "ig",
        "id-ID",
        "id",
        "ga-IE",
        "ga",
        "it-IT",
        "it-CH",
        "it",
        "ja-JP",
        "ja",
        "kea-CV",
        "kea",
        "kab-DZ",
        "kab",
        "kl-GL",
        "kl",
        "kln-KE",
        "kln",
        "kam-KE",
        "kam",
        "kn-IN",
        "kn",
        "kk-Cyrl",
        "kk-Cyrl-KZ",
        "kk",
        "km-KH",
        "km",
        "ki-KE",
        "ki",
        "rw-RW",
        "rw",
        "kok-IN",
        "kok",
        "ko-KR",
        "ko",
        "khq-ML",
        "khq",
        "ses-ML",
        "ses",
        "lag-TZ",
        "lag",
        "lv-LV",
        "lv",
        "lt-LT",
        "lt",
        "luo-KE",
        "luo",
        "luy-KE",
        "luy",
        "mk-MK",
        "mk",
        "jmc-TZ",
        "jmc",
        "kde-TZ",
        "kde",
        "mg-MG",
        "mg",
        "ms-BN",
        "ms-MY",
        "ms",
        "ml-IN",
        "ml",
        "mt-MT",
        "mt",
        "gv-GB",
        "gv",
        "mr-IN",
        "mr",
        "mas-KE",
        "mas-TZ",
        "mas",
        "mer-KE",
        "mer",
        "mfe-MU",
        "mfe",
        "naq-NA",
        "naq",
        "ne-IN",
        "ne-NP",
        "ne",
        "nd-ZW",
        "nd",
        "nb-NO",
        "nb",
        "nn-NO",
        "nn",
        "nyn-UG",
        "nyn",
        "or-IN",
        "or",
        "om-ET",
        "om-KE",
        "om",
        "ps-AF",
        "ps",
        "fa-AF",
        "fa-IR",
        "fa",
        "pl-PL",
        "pl",
        "pt-BR",
        "pt-GW",
        "pt-MZ",
        "pt-PT",
        "pt",
        "pa-Arab",
        "pa-Arab-PK",
        "pa-Guru",
        "pa-Guru-IN",
        "pa",
        "ro-MD",
        "ro-RO",
        "ro",
        "rm-CH",
        "rm",
        "rof-TZ",
        "rof",
        "ru-MD",
        "ru-RU",
        "ru-UA",
        "ru",
        "rwk-TZ",
        "rwk",
        "saq-KE",
        "saq",
        "sg-CF",
        "sg",
        "seh-MZ",
        "seh",
        "sr-Cyrl",
        "sr-Cyrl-BA",
        "sr-Cyrl-ME",
        "sr-Cyrl-RS",
        "sr-Latn",
        "sr-Latn-BA",
        "sr-Latn-ME",
        "sr-Latn-RS",
        "sr",
        "sn-ZW",
        "sn",
        "ii-CN",
        "ii",
        "si-LK",
        "si",
        "sk-SK",
        "sk",
        "sl-SI",
        "sl",
        "xog-UG",
        "xog",
        "so-DJ",
        "so-ET",
        "so-KE",
        "so-SO",
        "so",
        "es-AR",
        "es-BO",
        "es-CL",
        "es-CO",
        "es-CR",
        "es-DO",
        "es-EC",
        "es-SV",
        "es-GQ",
        "es-GT",
        "es-HN",
        "es-419",
        "es-MX",
        "es-NI",
        "es-PA",
        "es-PY",
        "es-PE",
        "es-PR",
        "es-ES",
        "es-US",
        "es-UY",
        "es-VE",
        "es",
        "sw-KE",
        "sw-TZ",
        "sw",
        "sv-FI",
        "sv-SE",
        "sv",
        "gsw-CH",
        "gsw",
        "shi-Latn",
        "shi-Latn-MA",
        "shi-Tfng",
        "shi-Tfng-MA",
        "shi",
        "dav-KE",
        "dav",
        "ta-IN",
        "ta-LK",
        "ta",
        "te-IN",
        "te",
        "teo-KE",
        "teo-UG",
        "teo",
        "th-TH",
        "th",
        "bo-CN",
        "bo-IN",
        "bo",
        "ti-ER",
        "ti-ET",
        "ti",
        "to-TO",
        "to",
        "tr-TR",
        "tr",
        "uk-UA",
        "uk",
        "ur-IN",
        "ur-PK",
        "ur",
        "uz-Arab",
        "uz-Arab-AF",
        "uz-Cyrl",
        "uz-Cyrl-UZ",
        "uz-Latn",
        "uz-Latn-UZ",
        "uz",
        "vi-VN",
        "vi",
        "vun-TZ",
        "vun",
        "cy-GB",
        "cy",
        "yo-NG",
        "yo",
        "zu-ZA",
        "zu",
    ];

    /**
     * @var array
     */
    protected $_languageByLocale = [
        "af-NA"       =>  "Afrikaans (Namibia)",
        "af-ZA"       =>  "Afrikaans (South Africa)",
        "af"          =>  "Afrikaans",
        "ak-GH"       =>  "Akan (Ghana)",
        "ak"          =>  "Akan",
        "sq-AL"       =>  "Albanian (Albania)",
        "sq"          =>  "Albanian",
        "am-ET"       =>  "Amharic (Ethiopia)",
        "am"          =>  "Amharic",
        "ar-DZ"       =>  "Arabic (Algeria)",
        "ar-BH"       =>  "Arabic (Bahrain)",
        "ar-EG"       =>  "Arabic (Egypt)",
        "ar-IQ"       =>  "Arabic (Iraq)",
        "ar-JO"       =>  "Arabic (Jordan)",
        "ar-KW"       =>  "Arabic (Kuwait)",
        "ar-LB"       =>  "Arabic (Lebanon)",
        "ar-LY"       =>  "Arabic (Libya)",
        "ar-MA"       =>  "Arabic (Morocco)",
        "ar-OM"       =>  "Arabic (Oman)",
        "ar-QA"       =>  "Arabic (Qatar)",
        "ar-SA"       =>  "Arabic (Saudi Arabia)",
        "ar-SD"       =>  "Arabic (Sudan)",
        "ar-SY"       =>  "Arabic (Syria)",
        "ar-TN"       =>  "Arabic (Tunisia)",
        "ar-AE"       =>  "Arabic (United Arab Emirates)",
        "ar-YE"       =>  "Arabic (Yemen)",
        "ar"          =>  "Arabic",
        "hy-AM"       =>  "Armenian (Armenia)",
        "hy"          =>  "Armenian",
        "as-IN"       =>  "Assamese (India)",
        "as"          =>  "Assamese",
        "asa-TZ"      =>  "Asu (Tanzania)",
        "asa"         =>  "Asu",
        "az-Cyrl"     =>  "Azerbaijani (Cyrillic)",
        "az-Cyrl-AZ"  =>  "Azerbaijani (Cyrillic, Azerbaijan)",
        "az-Latn"     =>  "Azerbaijani (Latin)",
        "az-Latn-AZ"  =>  "Azerbaijani (Latin, Azerbaijan)",
        "az"          =>  "Azerbaijani",
        "bm-ML"       =>  "Bambara (Mali)",
        "bm"          =>  "Bambara",
        "eu-ES"       =>  "Basque (Spain)",
        "eu"          =>  "Basque",
        "be-BY"       =>  "Belarusian (Belarus)",
        "be"          =>  "Belarusian",
        "bem-ZM"      =>  "Bemba (Zambia)",
        "bem"         =>  "Bemba",
        "bez-TZ"      =>  "Bena (Tanzania)",
        "bez"         =>  "Bena",
        "bn-BD"       =>  "Bengali (Bangladesh)",
        "bn-IN"       =>  "Bengali (India)",
        "bn"          =>  "Bengali",
        "bs-BA"       =>  "Bosnian (Bosnia and Herzegovina)",
        "bs"          =>  "Bosnian",
        "bg-BG"       =>  "Bulgarian (Bulgaria)",
        "bg"          =>  "Bulgarian",
        "my-MM"       =>  "Burmese (Myanmar [Burma])",
        "my"          =>  "Burmese",
        "ca-ES"       =>  "Catalan (Spain)",
        "ca"          =>  "Catalan",
        "tzm-Latn"    =>  "Central Morocco Tamazight (Latin)",
        "tzm-Latn-MA" =>  "Central Morocco Tamazight (Latin, Morocco)",
        "tzm"         =>  "Central Morocco Tamazight",
        "chr-US"      =>  "Cherokee (United States)",
        "chr"         =>  "Cherokee",
        "cgg-UG"      =>  "Chiga (Uganda)",
        "cgg"         =>  "Chiga",
        "zh-Hans"     =>  "Chinese (Simplified Han)",
        "zh-Hans-CN"  =>  "Chinese (Simplified Han, China)",
        "zh-Hans-HK"  =>  "Chinese (Simplified Han, Hong Kong SAR China)",
        "zh-Hans-MO"  =>  "Chinese (Simplified Han, Macau SAR China)",
        "zh-Hans-SG"  =>  "Chinese (Simplified Han, Singapore)",
        "zh-Hant"     =>  "Chinese (Traditional Han)",
        "zh-Hant-HK"  =>  "Chinese (Traditional Han, Hong Kong SAR China)",
        "zh-Hant-MO"  =>  "Chinese (Traditional Han, Macau SAR China)",
        "zh-Hant-TW"  =>  "Chinese (Traditional Han, Taiwan)",
        "zh"          =>  "Chinese",
        "kw-GB"       =>  "Cornish (United Kingdom)",
        "kw"          =>  "Cornish",
        "hr-HR"       =>  "Croatian (Croatia)",
        "hr"          =>  "Croatian",
        "cs-CZ"       =>  "Czech (Czech Republic)",
        "cs"          =>  "Czech",
        "da-DK"       =>  "Danish (Denmark)",
        "da"          =>  "Danish",
        "nl-BE"       =>  "Dutch (Belgium)",
        "nl-NL"       =>  "Dutch (Netherlands)",
        "nl"          =>  "Dutch",
        "ebu-KE"      =>  "Embu (Kenya)",
        "ebu"         =>  "Embu",
        "en-AS"       =>  "English (American Samoa)",
        "en-AU"       =>  "English (Australia)",
        "en-BE"       =>  "English (Belgium)",
        "en-BZ"       =>  "English (Belize)",
        "en-BW"       =>  "English (Botswana)",
        "en-CA"       =>  "English (Canada)",
        "en-GU"       =>  "English (Guam)",
        "en-HK"       =>  "English (Hong Kong SAR China)",
        "en-IN"       =>  "English (India)",
        "en-IE"       =>  "English (Ireland)",
        "en-JM"       =>  "English (Jamaica)",
        "en-MT"       =>  "English (Malta)",
        "en-MH"       =>  "English (Marshall Islands)",
        "en-MU"       =>  "English (Mauritius)",
        "en-NA"       =>  "English (Namibia)",
        "en-NZ"       =>  "English (New Zealand)",
        "en-MP"       =>  "English (Northern Mariana Islands)",
        "en-PK"       =>  "English (Pakistan)",
        "en-PH"       =>  "English (Philippines)",
        "en-SG"       =>  "English (Singapore)",
        "en-ZA"       =>  "English (South Africa)",
        "en-TT"       =>  "English (Trinidad and Tobago)",
        "en-UM"       =>  "English (U.S. Minor Outlying Islands)",
        "en-VI"       =>  "English (U.S. Virgin Islands)",
        "en-GB"       =>  "English (United Kingdom)",
        "en-US"       =>  "English (United States)",
        "en-ZW"       =>  "English (Zimbabwe)",
        "en"          =>  "English",
        "eo"          =>  "Esperanto",
        "et-EE"       =>  "Estonian (Estonia)",
        "et"          =>  "Estonian",
        "ee-GH"       =>  "Ewe (Ghana)",
        "ee-TG"       =>  "Ewe (Togo)",
        "ee"          =>  "Ewe",
        "fo-FO"       =>  "Faroese (Faroe Islands)",
        "fo"          =>  "Faroese",
        "fil-PH"      =>  "Filipino (Philippines)",
        "fil"         =>  "Filipino",
        "fi-FI"       =>  "Finnish (Finland)",
        "fi"          =>  "Finnish",
        "fr-BE"       =>  "French (Belgium)",
        "fr-BJ"       =>  "French (Benin)",
        "fr-BF"       =>  "French (Burkina Faso)",
        "fr-BI"       =>  "French (Burundi)",
        "fr-CM"       =>  "French (Cameroon)",
        "fr-CA"       =>  "French (Canada)",
        "fr-CF"       =>  "French (Central African Republic)",
        "fr-TD"       =>  "French (Chad)",
        "fr-KM"       =>  "French (Comoros)",
        "fr-CG"       =>  "French (Congo - Brazzaville)",
        "fr-CD"       =>  "French (Congo - Kinshasa)",
        "fr-CI"       =>  "French (Côte d’Ivoire)",
        "fr-DJ"       =>  "French (Djibouti)",
        "fr-GQ"       =>  "French (Equatorial Guinea)",
        "fr-FR"       =>  "French (France)",
        "fr-GA"       =>  "French (Gabon)",
        "fr-GP"       =>  "French (Guadeloupe)",
        "fr-GN"       =>  "French (Guinea)",
        "fr-LU"       =>  "French (Luxembourg)",
        "fr-MG"       =>  "French (Madagascar)",
        "fr-ML"       =>  "French (Mali)",
        "fr-MQ"       =>  "French (Martinique)",
        "fr-MC"       =>  "French (Monaco)",
        "fr-NE"       =>  "French (Niger)",
        "fr-RW"       =>  "French (Rwanda)",
        "fr-RE"       =>  "French (Réunion)",
        "fr-BL"       =>  "French (Saint Barthélemy)",
        "fr-MF"       =>  "French (Saint Martin)",
        "fr-SN"       =>  "French (Senegal)",
        "fr-CH"       =>  "French (Switzerland)",
        "fr-TG"       =>  "French (Togo)",
        "fr"          =>  "French",
        "ff-SN"       =>  "Fulah (Senegal)",
        "ff"          =>  "Fulah",
        "gl-ES"       =>  "Galician (Spain)",
        "gl"          =>  "Galician",
        "lg-UG"       =>  "Ganda (Uganda)",
        "lg"          =>  "Ganda",
        "ka-GE"       =>  "Georgian (Georgia)",
        "ka"          =>  "Georgian",
        "de-AT"       =>  "German (Austria)",
        "de-BE"       =>  "German (Belgium)",
        "de-DE"       =>  "German (Germany)",
        "de-LI"       =>  "German (Liechtenstein)",
        "de-LU"       =>  "German (Luxembourg)",
        "de-CH"       =>  "German (Switzerland)",
        "de"          =>  "German",
        "el-CY"       =>  "Greek (Cyprus)",
        "el-GR"       =>  "Greek (Greece)",
        "el"          =>  "Greek",
        "gu-IN"       =>  "Gujarati (India)",
        "gu"          =>  "Gujarati",
        "guz-KE"      =>  "Gusii (Kenya)",
        "guz"         =>  "Gusii",
        "ha-Latn"     =>  "Hausa (Latin)",
        "ha-Latn-GH"  =>  "Hausa (Latin, Ghana)",
        "ha-Latn-NE"  =>  "Hausa (Latin, Niger)",
        "ha-Latn-NG"  =>  "Hausa (Latin, Nigeria)",
        "ha"          =>  "Hausa",
        "haw-US"      =>  "Hawaiian (United States)",
        "haw"         =>  "Hawaiian",
        "he-IL"       =>  "Hebrew (Israel)",
        "he"          =>  "Hebrew",
        "hi-IN"       =>  "Hindi (India)",
        "hi"          =>  "Hindi",
        "hu-HU"       =>  "Hungarian (Hungary)",
        "hu"          =>  "Hungarian",
        "is-IS"       =>  "Icelandic (Iceland)",
        "is"          =>  "Icelandic",
        "ig-NG"       =>  "Igbo (Nigeria)",
        "ig"          =>  "Igbo",
        "id-ID"       =>  "Indonesian (Indonesia)",
        "id"          =>  "Indonesian",
        "ga-IE"       =>  "Irish (Ireland)",
        "ga"          =>  "Irish",
        "it-IT"       =>  "Italian (Italy)",
        "it-CH"       =>  "Italian (Switzerland)",
        "it"          =>  "Italian",
        "ja-JP"       =>  "Japanese (Japan)",
        "ja"          =>  "Japanese",
        "kea-CV"      =>  "Kabuverdianu (Cape Verde)",
        "kea"         =>  "Kabuverdianu",
        "kab-DZ"      =>  "Kabyle (Algeria)",
        "kab"         =>  "Kabyle",
        "kl-GL"       =>  "Kalaallisut (Greenland)",
        "kl"          =>  "Kalaallisut",
        "kln-KE"      =>  "Kalenjin (Kenya)",
        "kln"         =>  "Kalenjin",
        "kam-KE"      =>  "Kamba (Kenya)",
        "kam"         =>  "Kamba",
        "kn-IN"       =>  "Kannada (India)",
        "kn"          =>  "Kannada",
        "kk-Cyrl"     =>  "Kazakh (Cyrillic)",
        "kk-Cyrl-KZ"  =>  "Kazakh (Cyrillic, Kazakhstan)",
        "kk"          =>  "Kazakh",
        "km-KH"       =>  "Khmer (Cambodia)",
        "km"          =>  "Khmer",
        "ki-KE"       =>  "Kikuyu (Kenya)",
        "ki"          =>  "Kikuyu",
        "rw-RW"       =>  "Kinyarwanda (Rwanda)",
        "rw"          =>  "Kinyarwanda",
        "kok-IN"      =>  "Konkani (India)",
        "kok"         =>  "Konkani",
        "ko-KR"       =>  "Korean (South Korea)",
        "ko"          =>  "Korean",
        "khq-ML"      =>  "Koyra Chiini (Mali)",
        "khq"         =>  "Koyra Chiini",
        "ses-ML"      =>  "Koyraboro Senni (Mali)",
        "ses"         =>  "Koyraboro Senni",
        "lag-TZ"      =>  "Langi (Tanzania)",
        "lag"         =>  "Langi",
        "lv-LV"       =>  "Latvian (Latvia)",
        "lv"          =>  "Latvian",
        "lt-LT"       =>  "Lithuanian (Lithuania)",
        "lt"          =>  "Lithuanian",
        "luo-KE"      =>  "Luo (Kenya)",
        "luo"         =>  "Luo",
        "luy-KE"      =>  "Luyia (Kenya)",
        "luy"         =>  "Luyia",
        "mk-MK"       =>  "Macedonian (Macedonia)",
        "mk"          =>  "Macedonian",
        "jmc-TZ"      =>  "Machame (Tanzania)",
        "jmc"         =>  "Machame",
        "kde-TZ"      =>  "Makonde (Tanzania)",
        "kde"         =>  "Makonde",
        "mg-MG"       =>  "Malagasy (Madagascar)",
        "mg"          =>  "Malagasy",
        "ms-BN"       =>  "Malay (Brunei)",
        "ms-MY"       =>  "Malay (Malaysia)",
        "ms"          =>  "Malay",
        "ml-IN"       =>  "Malayalam (India)",
        "ml"          =>  "Malayalam",
        "mt-MT"       =>  "Maltese (Malta)",
        "mt"          =>  "Maltese",
        "gv-GB"       =>  "Manx (United Kingdom)",
        "gv"          =>  "Manx",
        "mr-IN"       =>  "Marathi (India)",
        "mr"          =>  "Marathi",
        "mas-KE"      =>  "Masai (Kenya)",
        "mas-TZ"      =>  "Masai (Tanzania)",
        "mas"         =>  "Masai",
        "mer-KE"      =>  "Meru (Kenya)",
        "mer"         =>  "Meru",
        "mfe-MU"      =>  "Morisyen (Mauritius)",
        "mfe"         =>  "Morisyen",
        "naq-NA"      =>  "Nama (Namibia)",
        "naq"         =>  "Nama",
        "ne-IN"       =>  "Nepali (India)",
        "ne-NP"       =>  "Nepali (Nepal)",
        "ne"          =>  "Nepali",
        "nd-ZW"       =>  "North Ndebele (Zimbabwe)",
        "nd"          =>  "North Ndebele",
        "nb-NO"       =>  "Norwegian Bokmål (Norway)",
        "nb"          =>  "Norwegian Bokmål",
        "nn-NO"       =>  "Norwegian Nynorsk (Norway)",
        "nn"          =>  "Norwegian Nynorsk",
        "nyn-UG"      =>  "Nyankole (Uganda)",
        "nyn"         =>  "Nyankole",
        "or-IN"       =>  "Oriya (India)",
        "or"          =>  "Oriya",
        "om-ET"       =>  "Oromo (Ethiopia)",
        "om-KE"       =>  "Oromo (Kenya)",
        "om"          =>  "Oromo",
        "ps-AF"       =>  "Pashto (Afghanistan)",
        "ps"          =>  "Pashto",
        "fa-AF"       =>  "Persian (Afghanistan)",
        "fa-IR"       =>  "Persian (Iran)",
        "fa"          =>  "Persian",
        "pl-PL"       =>  "Polish (Poland)",
        "pl"          =>  "Polish",
        "pt-BR"       =>  "Portuguese (Brazil)",
        "pt-GW"       =>  "Portuguese (Guinea-Bissau)",
        "pt-MZ"       =>  "Portuguese (Mozambique)",
        "pt-PT"       =>  "Portuguese (Portugal)",
        "pt"          =>  "Portuguese",
        "pa-Arab"     =>  "Punjabi (Arabic)",
        "pa-Arab-PK"  =>  "Punjabi (Arabic, Pakistan)",
        "pa-Guru"     =>  "Punjabi (Gurmukhi)",
        "pa-Guru-IN"  =>  "Punjabi (Gurmukhi, India)",
        "pa"          =>  "Punjabi",
        "ro-MD"       =>  "Romanian (Moldova)",
        "ro-RO"       =>  "Romanian (Romania)",
        "ro"          =>  "Romanian",
        "rm-CH"       =>  "Romansh (Switzerland)",
        "rm"          =>  "Romansh",
        "rof-TZ"      =>  "Rombo (Tanzania)",
        "rof"         =>  "Rombo",
        "ru-MD"       =>  "Russian (Moldova)",
        "ru-RU"       =>  "Russian (Russia)",
        "ru-UA"       =>  "Russian (Ukraine)",
        "ru"          =>  "Russian",
        "rwk-TZ"      =>  "Rwa (Tanzania)",
        "rwk"         =>  "Rwa",
        "saq-KE"      =>  "Samburu (Kenya)",
        "saq"         =>  "Samburu",
        "sg-CF"       =>  "Sango (Central African Republic)",
        "sg"          =>  "Sango",
        "seh-MZ"      =>  "Sena (Mozambique)",
        "seh"         =>  "Sena",
        "sr-Cyrl"     =>  "Serbian (Cyrillic)",
        "sr-Cyrl-BA"  =>  "Serbian (Cyrillic, Bosnia and Herzegovina)",
        "sr-Cyrl-ME"  =>  "Serbian (Cyrillic, Montenegro)",
        "sr-Cyrl-RS"  =>  "Serbian (Cyrillic, Serbia)",
        "sr-Latn"     =>  "Serbian (Latin)",
        "sr-Latn-BA"  =>  "Serbian (Latin, Bosnia and Herzegovina)",
        "sr-Latn-ME"  =>  "Serbian (Latin, Montenegro)",
        "sr-Latn-RS"  =>  "Serbian (Latin, Serbia)",
        "sr"          =>  "Serbian",
        "sn-ZW"       =>  "Shona (Zimbabwe)",
        "sn"          =>  "Shona",
        "ii-CN"       =>  "Sichuan Yi (China)",
        "ii"          =>  "Sichuan Yi",
        "si-LK"       =>  "Sinhala (Sri Lanka)",
        "si"          =>  "Sinhala",
        "sk-SK"       =>  "Slovak (Slovakia)",
        "sk"          =>  "Slovak",
        "sl-SI"       =>  "Slovenian (Slovenia)",
        "sl"          =>  "Slovenian",
        "xog-UG"      =>  "Soga (Uganda)",
        "xog"         =>  "Soga",
        "so-DJ"       =>  "Somali (Djibouti)",
        "so-ET"       =>  "Somali (Ethiopia)",
        "so-KE"       =>  "Somali (Kenya)",
        "so-SO"       =>  "Somali (Somalia)",
        "so"          =>  "Somali",
        "es-AR"       =>  "Spanish (Argentina)",
        "es-BO"       =>  "Spanish (Bolivia)",
        "es-CL"       =>  "Spanish (Chile)",
        "es-CO"       =>  "Spanish (Colombia)",
        "es-CR"       =>  "Spanish (Costa Rica)",
        "es-DO"       =>  "Spanish (Dominican Republic)",
        "es-EC"       =>  "Spanish (Ecuador)",
        "es-SV"       =>  "Spanish (El Salvador)",
        "es-GQ"       =>  "Spanish (Equatorial Guinea)",
        "es-GT"       =>  "Spanish (Guatemala)",
        "es-HN"       =>  "Spanish (Honduras)",
        "es-419"      =>  "Spanish (Latin America)",
        "es-MX"       =>  "Spanish (Mexico)",
        "es-NI"       =>  "Spanish (Nicaragua)",
        "es-PA"       =>  "Spanish (Panama)",
        "es-PY"       =>  "Spanish (Paraguay)",
        "es-PE"       =>  "Spanish (Peru)",
        "es-PR"       =>  "Spanish (Puerto Rico)",
        "es-ES"       =>  "Spanish (Spain)",
        "es-US"       =>  "Spanish (United States)",
        "es-UY"       =>  "Spanish (Uruguay)",
        "es-VE"       =>  "Spanish (Venezuela)",
        "es"          =>  "Spanish",
        "sw-KE"       =>  "Swahili (Kenya)",
        "sw-TZ"       =>  "Swahili (Tanzania)",
        "sw"          =>  "Swahili",
        "sv-FI"       =>  "Swedish (Finland)",
        "sv-SE"       =>  "Swedish (Sweden)",
        "sv"          =>  "Swedish",
        "gsw-CH"      =>  "Swiss German (Switzerland)",
        "gsw"         =>  "Swiss German",
        "shi-Latn"    =>  "Tachelhit (Latin)",
        "shi-Latn-MA" =>  "Tachelhit (Latin, Morocco)",
        "shi-Tfng"    =>  "Tachelhit (Tifinagh)",
        "shi-Tfng-MA" =>  "Tachelhit (Tifinagh, Morocco)",
        "shi"         =>  "Tachelhit",
        "dav-KE"      =>  "Taita (Kenya)",
        "dav"         =>  "Taita",
        "ta-IN"       =>  "Tamil (India)",
        "ta-LK"       =>  "Tamil (Sri Lanka)",
        "ta"          =>  "Tamil",
        "te-IN"       =>  "Telugu (India)",
        "te"          =>  "Telugu",
        "teo-KE"      =>  "Teso (Kenya)",
        "teo-UG"      =>  "Teso (Uganda)",
        "teo"         =>  "Teso",
        "th-TH"       =>  "Thai (Thailand)",
        "th"          =>  "Thai",
        "bo-CN"       =>  "Tibetan (China)",
        "bo-IN"       =>  "Tibetan (India)",
        "bo"          =>  "Tibetan",
        "ti-ER"       =>  "Tigrinya (Eritrea)",
        "ti-ET"       =>  "Tigrinya (Ethiopia)",
        "ti"          =>  "Tigrinya",
        "to-TO"       =>  "Tonga (Tonga)",
        "to"          =>  "Tonga",
        "tr-TR"       =>  "Turkish (Turkey)",
        "tr"          =>  "Turkish",
        "uk-UA"       =>  "Ukrainian (Ukraine)",
        "uk"          =>  "Ukrainian",
        "ur-IN"       =>  "Urdu (India)",
        "ur-PK"       =>  "Urdu (Pakistan)",
        "ur"          =>  "Urdu",
        "uz-Arab"     =>  "Uzbek (Arabic)",
        "uz-Arab-AF"  =>  "Uzbek (Arabic, Afghanistan)",
        "uz-Cyrl"     =>  "Uzbek (Cyrillic)",
        "uz-Cyrl-UZ"  =>  "Uzbek (Cyrillic, Uzbekistan)",
        "uz-Latn"     =>  "Uzbek (Latin)",
        "uz-Latn-UZ"  =>  "Uzbek (Latin, Uzbekistan)",
        "uz"          =>  "Uzbek",
        "vi-VN"       =>  "Vietnamese (Vietnam)",
        "vi"          =>  "Vietnamese",
        "vun-TZ"      =>  "Vunjo (Tanzania)",
        "vun"         =>  "Vunjo",
        "cy-GB"       =>  "Welsh (United Kingdom)",
        "cy"          =>  "Welsh",
        "yo-NG"       =>  "Yoruba (Nigeria)",
        "yo"          =>  "Yoruba",
        "zu-ZA"       =>  "Zulu (South Africa)",
        "zu"          =>  "Zulu",
    ];
}