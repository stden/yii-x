<?php
/**
 * Работа с кодами стран
 */
class CountryX
{
    /**
     * @static
     * @param $country_code
     * @return bool Говорят ли там по-русски?
     */
    public static function understand_russian($country_code)
    {
        switch (strtoupper($country_code)) {
            case 'RU':
                return true;
            default:
                return false;
        }
    }
}
