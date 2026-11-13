<?php
/**
 * Обработка запросов Request
 */
class MyForm
{

    /**
     * Значение целого типа из формы
     * @static
     * @param string $param_name
     * @param int $default
     * @return int
     */
    public static function Int($param_name, $default = 0)
    {
        if (isset($_REQUEST[$param_name]))
            return intval($_REQUEST[$param_name]);
        else
            return $default;
    }

    /**
     * Значение булевого типа из формы
     * @static
     * @param string $param_name
     * @param bool $default
     * @return bool
     */
    public static function Boolean($param_name, $default = false)
    {
        if (isset($_REQUEST[$param_name]))
            return $_REQUEST[$param_name] == true; // Это так и должно быть :)
        else
            return $default;
    }

}
