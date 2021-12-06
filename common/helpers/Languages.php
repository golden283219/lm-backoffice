<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 08.11.2019
 * Time: 15:36
 */

namespace common\helpers;


class Languages
{
    const suggested_languages = [
        'en', 'es', 'da', 'fr', 'de', 'sv', 'ru', 'pl', 'uk'
    ];

    const languages = [
        'English' => 'en',
        'Afar' => 'aa',
        'Abkhazian' => 'ab',
        'Afrikaans' => 'af',
        'Amharic' => 'am',
        'Arabic' => 'ar',
        'Assamese' => 'as',
        'Aymara' => 'ay',
        'Azerbaijani' => 'az',
        'Bashkir' => 'ba',
        'Belarusian' => 'be',
        'Bulgarian' => 'bg',
        'Bihari' => 'bh',
        'Bislama' => 'bi',
        'Tibetan' => 'bo',
        'Breton' => 'br',
        'Catalan' => 'ca',
        'Corsican' => 'co',
        'Czech' => 'cs',
        'Welsh' => 'cy',
        'Danish' => 'da',
        'German' => 'de',
        'Bhutani' => 'dz',
        'Greek' => 'el',
        'Esperanto' => 'eo',
        'Spanish' => 'es',
        'Estonian' => 'et',
        'Basque' => 'eu',
        'Persian' => 'fa',
        'Finnish' => 'fi',
        'Fiji' => 'fj',
        'Faeroese' => 'fo',
        'French' => 'fr',
        'Frisian' => 'fy',
        'Irish' => 'ga',
        'Galician' => 'gl',
        'Guarani' => 'gn',
        'Gujarati' => 'gu',
        'Hausa' => 'ha',
        'Hindi' => 'hi',
        'Croatian' => 'hr',
        'Hungarian' => 'hu',
        'Armenian' => 'hy',
        'Interlingua' => 'ia',
        'Interlingue' => 'ie',
        'Inupiak' => 'ik',
        'Indonesian' => 'in',
        'Icelandic' => 'is',
        'Italian' => 'it',
        'Hebrew' => 'iw',
        'Japanese' => 'ja',
        'Yiddish' => 'ji',
        'Javanese' => 'jw',
        'Georgian' => 'ka',
        'Kazakh' => 'kk',
        'Greenlandic' => 'kl',
        'Cambodian' => 'km',
        'Kannada' => 'kn',
        'Korean' => 'ko',
        'Kashmiri' => 'ks',
        'Kurdish' => 'ku',
        'Kirghiz' => 'ky',
        'Latin' => 'la',
        'Lingala' => 'ln',
        'Laothian' => 'lo',
        'Lithuanian' => 'lt',
        'Malagasy' => 'mg',
        'Maori' => 'mi',
        'Macedonian' => 'mk',
        'Malayalam' => 'ml',
        'Mongolian' => 'mn',
        'Moldavian' => 'mo',
        'Marathi' => 'mr',
        'Malay' => 'ms',
        'Maltese' => 'mt',
        'Burmese' => 'my',
        'Nauru' => 'na',
        'Nepali' => 'ne',
        'Dutch' => 'nl',
        'Norwegian' => 'no',
        'Occitan' => 'oc',
        'Punjabi' => 'pa',
        'Polish' => 'pl',
        'Portuguese' => 'pt',
        'Quechua' => 'qu',
        'Kirundi' => 'rn',
        'Romanian' => 'ro',
        'Russian' => 'ru',
        'Kinyarwanda' => 'rw',
        'Sanskrit' => 'sa',
        'Sindhi' => 'sd',
        'Sangro' => 'sg',
        'Singhalese' => 'si',
        'Slovak' => 'sk',
        'Slovenian' => 'sl',
        'Samoan' => 'sm',
        'Shona' => 'sn',
        'Somali' => 'so',
        'Albanian' => 'sq',
        'Serbian' => 'sr',
        'Siswati' => 'ss',
        'Sesotho' => 'st',
        'Sundanese' => 'su',
        'Swedish' => 'sv',
        'Swahili' => 'sw',
        'Tamil' => 'ta',
        'Telugu' => 'te',
        'Tajik' => 'tg',
        'Thai' => 'th',
        'Tigrinya' => 'ti',
        'Turkmen' => 'tk',
        'Tagalog' => 'tl',
        'Setswana' => 'tn',
        'Tonga' => 'to',
        'Turkish' => 'tr',
        'Tsonga' => 'ts',
        'Tatar' => 'tt',
        'Twi' => 'tw',
        'Ukrainian' => 'uk',
        'Urdu' => 'ur',
        'Uzbek' => 'uz',
        'Vietnamese' => 'vi',
        'Volapuk' => 'vo',
        'Wolof' => 'wo',
        'Xhosa' => 'xh',
        'Yoruba' => 'yo',
        'Chinese' => 'zh',
        'Zulu' => 'zu'
    ];

    public static function get_iso_by_name ($name)
    {
        if (in_array($name, array_values(self::languages))) {
            return $name;
        }

        $name = ucfirst(strtolower($name));

        if (isset(self::languages[$name])) {
            return self::languages[$name];
        }

        return null;
    }

    public static function get_name_by_iso ($iso)
    {
        $languages_flipped = array_flip(self::languages);

        if (isset($languages_flipped[$iso])) {
            return $languages_flipped[$iso];
        }

        return null;
    }
}