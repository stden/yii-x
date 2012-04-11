<?php
/**
 * Утилиты для работы со строками
 */
class Strings
{
    /**
     * @static Заканчивается ли строка $string на $test
     * @param $string Какую строку проверяем
     * @param $test Какую подстроку ищем?
     * @return bool true - если заканчивается
     */
    public static function endsWith($string, $test)
    {
        $strLen = strlen($string);
        $testLen = strlen($test);
        if ($testLen > $strLen) return false;
        return substr_compare($string, $test, -$testLen) === 0;
    }

}
