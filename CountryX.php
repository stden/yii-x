<?php
/**
 * Работа с кодами стран
 */
class CountryX
{
    /**
     * Коды стран взяты: http://countrycode.org
     * @static
     * @param $country_code string Полученный от пользователя код страны
     * @return bool Возвращать ли страницу по-русски? true - русский, false - english
     */
    public static function understand_russian($country_code)
    {
        switch (strtoupper(trim($country_code))) {
            case 'RU': // 1. Россия (Russia)
            case 'RUS':
            case 'EE': // 2. Эстония (Estonia)
            case 'EST':
            case 'LV': // 3. Латвия (Latvia)
            case 'LVA':
            case 'LT': // 4. Литва (Lithuania)
            case 'LTU':
            case 'BY': // 5. Беларусь (Belarus)
            case 'BLR':
            case 'UA': // 6. Украина (Ukraine)
            case 'UKR ':
            case 'MD': // 7. Молдова (Moldova)
            case 'MDA':
            case 'GE': // 8. Грузия (Georgia)
            case 'GEO':
            case 'AM': // 9. Армения (Armenia)
            case 'ARM':
            case 'AZ': // 10. Азербайджан (Azerbaijan)
            case 'AZE':
            case 'KZ': // 11. Казахстан (Kazakhstan)
            case 'KAZ':
            case 'UZ': // 12. Узбекистан (Uzbekistan)
            case 'UZB':
            case 'KI': // 13. Кыргызтан (Kyrgyzstan)
            case 'KIR':
            case 'TJ': // 14. Таджикистан (Tajikistan)
            case 'TJK':
            case 'TM': // 15. Туркменистан (Turkmenistan)
            case 'TKM':
                return true;
            default:
                return false;
        }
    }

    /**
     * @static
     * @return string Код страны
     */
    public static function Parse_HTTP_ACCEPT_LANGUAGE()
    {
        return 'RU';
    }

    public static function Parse()
    {
        // RFC 2616 compatible Accept Language Parser
        // http://www.ietf.org/rfc/rfc2616.txt, 14.4 Accept-Language, Page 104
        // Hypertext Transfer Protocol -- HTTP/1.1

        foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
            $pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})' .
                '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)' .
                '(?P<quantifier>\d\.\d))?$/';

            $splits = array();

            printf("Lang:,,%s''\n", $lang);
            if (preg_match($pattern, $lang, $splits)) {
                return [strtoupper($splits['primarytag']), strtoupper($splits['subtag'])];
            } else {
                return "\nno match\n";
            }
        }
    }

    public static function Lang()
    {
        $lang = Yii::app()->request->preferredLanguage;
        switch ($lang) {
            case 'ru_ru':
                return 'en';
            default:
                Yii::log('lang = ' . $lang);
                return 'en';
        }
    }
}
